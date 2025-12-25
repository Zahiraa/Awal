<?php

namespace App\Manager;

use App\Entity\Article;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\FileUploadService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ArticleManager
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
     * @throws \Exception
     */
    public function saveArticle(Article $article, $formStatus = 'drafted', $image = null, $isEdit = false, $media = null): Article
    {
    
        $this->setArticleStatus($article, $formStatus, $isEdit);

        if ($image) {
            $this->uploadImage($article, $image);
        }

        if ($media) {
            $this->uploadMedia($article, $media);
        }

        $this->entityManager->persist($article);
        $this->entityManager->flush();

        return $article;
    }

    private function uploadImage(Article $article, $image): void {
        if ($article->getImage()) {
            $oldImage =  $this->fileUploadService->delete($article->getImage()->getName());
            if ($oldImage) {
                $this->entityManager->remove($oldImage);
            }
        }

        $file = $this->fileUploadService->upload($image);
        $article->setImage($file);
    }

    private function uploadMedia(Article $article, $media): void {
        if ($article->getMedia()) {
            $oldMedia = $this->fileUploadService->delete($article->getMedia()->getName());
            if ($oldMedia) {
                $this->entityManager->remove($oldMedia);
            }
        }

        $file = $this->fileUploadService->upload($media);
        $article->setMedia($file);
    }

    /**
     * @throws \Exception
     * @throws TransportExceptionInterface
     */
    public function approveArticle(Article $article): Article
    {
        /** @var User $me */
        $me = $this->security->getUser();

        if (!$me->isAdmin()) {
            throw new \Exception('Vous n\'avez pas les droits pour approuver');
        }

        $article->setApprovedBy($me);
        $article->setStatut(Article::STATUT_PUBLISHED);

        $this->entityManager->flush();

        $user = $article->getUpdatedBy() ?? $article->getCreatedBy();

        $this->mailler->sendTemplateEmail(
            $user->getEmail(),
            'Article publié',
            'emails/article/approve_article.html.twig',
            [
                'nom_prenom' => $user,
                'titre' => $article->getTitre(),
                'nom_prenom_admin' => $me,
            ]
        );

        return $article;
    }

    /**
     * @throws TransportExceptionInterface
     */
    private function setArticleStatus(Article $article, $formStatus, $isEdit = false): void
    {
        /** @var User $me */
        $me = $this->security->getUser();

        if ($me->isManager()) {
            if ($formStatus === 'drafted') {
                $article->setStatut(Article::STATUT_DRAFT);
            } else {
                $article->setStatut(Article::STATUT_WAITING_APPROVAL);

                $user = $me;
                $subject = 'Article en attente d\'approbation';

                if ($isEdit) {
                    $subject = 'Article modifié et en attente d\'approbation';
                }

                $context = [
                    'nom_prenom' => $user,
                    'titre' => $article->getTitre(),
                    'isEdit' => $isEdit,
                    'url' => $isEdit ? $this->urlGenerator->generate('app_admin_article_show', ['id' => $article->getId()], UrlGeneratorInterface::ABSOLUTE_URL) : $this->urlGenerator->generate('app_admin_article_index', referenceType: UrlGeneratorInterface::ABSOLUTE_URL),
                ];

                $admins = $this->userRepository->findUsersByRoles(['ROLE_ADMIN']);

                foreach ($admins as $admin) {
                    $context['nom_prenom_admin'] = $admin;
                    $this->mailler->sendTemplateEmail( $admin->getEmail(), $subject, 'emails/article/new_and_edit_article.html.twig', $context );
                }
            }
        }
        if ($me->isAdmin()) {
            if ($formStatus === 'drafted') {
                $article->setStatut(Article::STATUT_DRAFT);
            }else {
                $article->setStatut(Article::STATUT_PUBLISHED);
            }
        }
    }

}