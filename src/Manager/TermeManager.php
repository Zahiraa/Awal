<?php

namespace App\Manager;

use App\Entity\Terme;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\FileUploadService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class TermeManager
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly Security $security,
        private readonly Mailler $mailler,
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly UserRepository $userRepository,
        private readonly FileUploadService $fileUploadService

    ) {}

    /**
     * @throws TransportExceptionInterface
     */
    public function saveTerme(Terme $terme, $formStatus = Terme::STATUT_DRAFT, UploadedFile $image = null): Terme
    {
        /** @var User $me */
        $me = $this->security->getUser();
        $terme->setCreatedBy($me);

        foreach ($terme->getContents() as $content) {
            $content->setCreatedBy($terme->getCreatedBy());
        }

        if($image){
            $file = $this->fileUploadService->upload($image);
            $terme->setImage($file);
        }
        $this->entityManager->persist($terme);

        $this->setTermeStatus($terme, $formStatus);

        $this->entityManager->flush();

        return $terme;
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function editTerme(Terme $terme, $formStatus = Terme::STATUT_DRAFT,  UploadedFile $image = null): Terme
    {
        /** @var User $me */
        $me = $this->security->getUser();
        $terme->setUpdatedBy($me);

        foreach ($terme->getContents() as $content) {
            if ($content->getCreatedBy() === null) {
                $content->setCreatedBy($me);
            } else {
                $content->setUpdatedBy($me);
            }
        }

        $this->setTermeStatus($terme, $formStatus, true);

        if ($image) {
            if ($terme->getImage()) {
                $oldImage =  $this->fileUploadService->delete($terme->getImage()->getName());
                if ($oldImage) {
                    $this->entityManager->remove($oldImage);
                }
            }
            $file = $this->fileUploadService->upload($image);
            $terme->setImage($file);
        }

        $this->entityManager->persist($terme);
        $this->entityManager->flush();

        return  $terme;
    }

    /**
     * @throws \Exception
     * @throws TransportExceptionInterface
     */
    public function approveTerme(Terme $terme): Terme
    {
        /** @var User $me */
        $me = $this->security->getUser();

        if (!$me->isAdmin()) {
            throw new \Exception('Vous n\'avez pas les droits pour approuver ce terme');
        }

        $terme->setApprovedBy($me);
        $terme->setApprovedAt(new \DateTime());
        $terme->setStatut(Terme::STATUT_PUBLISHED);

        $this->entityManager->flush();

        $user = $terme->getUpdatedBy() ?? $terme->getCreatedBy();

        $this->mailler->sendTemplateEmail(
            $user->getEmail(),
            'Terme publié',
            'emails/terme/approve_terme.html.twig',
            [
                'nom_prenom' => $user,
                'titre_fr' => $terme->getContents()->first()->getTitreFr(),
                'titre_ar' => $terme->getContents()->first()->getTitreAr(),
                'titre_dr' => $terme->getContents()->first()->getTitreDr(),
                'nom_prenom_admin' => $me,
            ]
        );

        return $terme;
    }

    /**
     * @throws TransportExceptionInterface
     */
    private function setTermeStatus(Terme $terme, $formStatus, $isEdit = false): void
    {
        /** @var User $me */
        $me = $this->security->getUser();

        // si c'est un manager, on met le terme en statut "waiting_approval" sauf s'il a juste sauvegardé alors, on met le terme en statut "draft"
        if ($me->isManager()) {
            if ($formStatus ===  Terme::STATUT_DRAFT) {
                $terme->setStatut(Terme::STATUT_DRAFT);
            } else {
                $terme->setStatut(Terme::STATUT_WAITING_APPROVAL);

                $user = $me;
                $subject = 'Terme en attente d\'approbation';

                if ($isEdit) {
                    $user = $terme->getUpdatedBy();
                    $subject = 'Terme modifié et en attente d\'approbation';
                }

                $context = [
                    'nom_prenom' => $user,
                    'titre_fr' => $terme->getContents()->first()->getTitreFr(),
                    'titre_ar' => $terme->getContents()->first()->getTitreAr(),
                    'titre_dr' => $terme->getContents()->first()->getTitreDr(),
                    'isEdit' => $isEdit,
                    'url' => $this->urlGenerator->generate('app_admin_terme_index', referenceType: UrlGeneratorInterface::ABSOLUTE_URL),
                ];

                $admins = $this->userRepository->findUsersByRoles(['ROLE_ADMIN']);

                foreach ($admins as $admin) {
                    $context['nom_prenom_admin'] = $admin;
                    $this->mailler->sendTemplateEmail( $admin->getEmail(), $subject, 'emails/terme/new_and_edit_terme.html.twig', $context );
                }
            }
        }

        // si c'est un admin, on met le terme en statut "published" sauf s'il a juste sauvegardé alors, on met le terme en statut "draft"
        if ($me->isAdmin()) {
            if ($formStatus ===  Terme::STATUT_DRAFT) {
                $terme->setStatut(Terme::STATUT_DRAFT);
            } else {
                $terme->setStatut(Terme::STATUT_PUBLISHED);
                $terme->setApprovedBy($me);
                $terme->setApprovedAt(new \DateTime());
            }
        }
    }

}