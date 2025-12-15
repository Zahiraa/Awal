<?php

namespace App\Manager;

use App\Entity\File;
use App\Entity\Proposition;
use App\Entity\Terme;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\FileUploadService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class PropositionManager
{
    public function __construct(
        private $uploadDirectory,
        private readonly EntityManagerInterface $entityManager,
        private readonly Security $security,
        private readonly Mailler $mailler,
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly UserRepository $userRepository,
        private readonly FileUploadService $fileUploadService,
        private SluggerInterface $slugger
    ) {}

    /**
     * @throws TransportExceptionInterface
     */
    public function saveProposition(Proposition $proposition, UploadedFile $image = null ): Proposition
    {
        /** @var User $me */
        $me = $this->security->getUser();

        $proposition->setCreatedBy($me);
        $proposition->setStatut(Proposition::STATUT_WAITING_APPROVAL);

        foreach ($proposition->getContents() as $content) {
            $content->setCreatedBy($proposition->getCreatedBy());
        }

        if($image){
            $file = $this->fileUploadService->upload($image);
            $proposition->setImage($file);
        }

        $this->entityManager->persist($proposition);
        $this->entityManager->flush();

        $users = $this->userRepository->findUsersByRoles(['ROLE_MANAGER', 'ROLE_ADMIN']);


        foreach ($users as $user) {
            $this->mailler->sendTemplateEmail(
                $user->getEmail(),
                'Nouvelle proposition',
                'emails/proposition/new_proposition.html.twig',
                [
                    'nom_prenom_admin' => $user,
                    'nom_prenom' => $proposition->getCreatedBy(),
                    'titre_fr' => $proposition->getContents()->first()->getTitreFr(),
                    'titre_ar' => $proposition->getContents()->first()->getTitreAr(),
                    'titre_dr' => $proposition->getContents()->first()->getTitreDr(),
                    'url' => $this->urlGenerator->generate('app_admin_proposition_show', ['id' => $proposition->getId()], UrlGeneratorInterface::ABSOLUTE_URL),
                ]
            );
        }

        return $proposition;
    }

    public function editProposition(Proposition $proposition, UploadedFile $image = null): Proposition
    {
        /** @var User $me */
        $me = $this->security->getUser();

        foreach ($proposition->getContents() as $content) {
            if ($content->getCreatedBy() === null) {
                $content->setCreatedBy($me);
            } else {
                $content->setUpdatedBy($me);
            }
        }

        if ($image) {
            if ($proposition->getImage()) {
                $oldImage =  $this->fileUploadService->delete($proposition->getImage()->getName());
                if ($oldImage) {
                    $this->entityManager->remove($oldImage);
                }
            }
            $file = $this->fileUploadService->upload($image);
            $proposition->setImage($file);
        }
        if($proposition->getStatut() == Proposition::STATUT_REJECTED ){
            $proposition->setStatut(Proposition::STATUT_WAITING_APPROVAL);
            $this->sendMailsAdmin($proposition);
        }

        $this->entityManager->persist($proposition);
        $this->entityManager->flush();

        return $proposition;
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function actionProposition(Proposition $proposition, string $action): Proposition
    {
        /** @var User $me */
        $me = $this->security->getUser();

        if (!$this->security->isGranted('ROLE_MANAGER')) {
            throw new \Exception('Vous n\'avez pas les droits pour gérer cette proposition');
        }

        if($action === 'approved'){
            $proposition->setStatut(Proposition::STATUT_APPROVED);
            $terme = new Terme();
            $terme->setStatut(Terme::STATUT_DRAFT);
            $terme->setCreatedBy($me);
            $terme->setProposition($proposition);
            $image = $proposition->getImage();
            if($image) {
                $file = $this->copyImage($image);
                $terme->setImage($file);
            }

            foreach ($proposition->getContents() as $content) {
                $content->setCreatedBy($me);
                $terme->addContent($content);
            }
            $this->entityManager->persist($terme);

        }else if($action === 'rejected') {
            $proposition->setStatut(Proposition::STATUT_REJECTED);
        }else {
            throw new \Exception('Action non reconnue');
        }

        $proposition->setActionBy($me);
        $this->entityManager->flush();

        $subject = $action === 'approved' ? 'Proposition approuvée' : 'Proposition rejetée';
        $this->mailler->sendTemplateEmail(
            $proposition->getCreatedBy()->getEmail(),
            $subject,
            'emails/proposition/action_on_proposition.html.twig',
            [
                'nom_prenom' => $proposition->getCreatedBy(),
                'status' => $proposition->getStatut(),
                'date' => $proposition->getActionBy()->getCreatedAt()->format('d/m/Y à H:i'),
                'titre_fr' => $proposition->getContents()->first()->getTitreFr(),
                'titre_ar' => $proposition->getContents()->first()->getTitreAr(),
                'titre_dr' => $proposition->getContents()->first()->getTitreDr(),
            ]
        );

        return $proposition;
    }


    private function copyImage(File $image): File
    {
        $originalImagePath = $this->uploadDirectory . '/' . $image->getName();
        if (!file_exists($originalImagePath)) {
            throw new \InvalidArgumentException("Original image file does not exist: " . $originalImagePath);
        }

        $fileExtension = pathinfo($originalImagePath, PATHINFO_EXTENSION);
        $safeFilename = $this->slugger->slug(pathinfo($image->getName(), PATHINFO_FILENAME));
        $fileName = $safeFilename . '-' . uniqid() . '.' . $fileExtension;
        $newImagePath = $this->uploadDirectory . '/' . $fileName;

        if (copy($originalImagePath, $newImagePath)) {
            $file = new File();
            $file->setName($fileName);
            $file->setSize((string) filesize($newImagePath));
            $file->setExtension($fileExtension);
            $this->entityManager->persist($file);

            return $file;
        } else {
            throw new \RuntimeException("Failed to copy image from '$originalImagePath' to '$newImagePath'");
        }
    }
    private function sendMailsAdmin($proposition){
        $users = $this->userRepository->findUsersByRoles(['ROLE_MANAGER', 'ROLE_ADMIN']);
            foreach ($users as $user) {
                $this->mailler->sendTemplateEmail(
                    $user->getEmail(),
                    'Mise à jour d une proposition déjà rejetée',
                    'emails/proposition/new_proposition.html.twig',
                    [
                        'nom_prenom_admin' => $user,
                        'nom_prenom' => $proposition->getCreatedBy(),
                        'titre_fr' => $proposition->getContents()->first()->getTitreFr(),
                        'titre_ar' => $proposition->getContents()->first()->getTitreAr(),
                        'titre_dr' => $proposition->getContents()->first()->getTitreDr(),
                        'url' => $this->urlGenerator->generate('app_admin_proposition_show', ['id' => $proposition->getId()], UrlGeneratorInterface::ABSOLUTE_URL),
                    ]
                );
            }
}
}