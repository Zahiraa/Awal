<?php

namespace App\Controller\Admin;

use App\Entity\ContenuDiscussion;
use App\Form\ContenuDiscussionType;
use App\Repository\ContenuDiscussionRepository;
use App\Service\FileUploadService;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/contenu/discussion')]
final class ContenuDiscussionController extends AbstractController
{
    #[Route('', name: 'app_admin_contenu_discussion_index', methods: ['GET'])]
    public function index(Request $request, PaginatorInterface $paginator, ContenuDiscussionRepository $contenuDiscussionRepository): Response
    {
        $search = $request->query->get('search', '');
        $queryBuilder = $contenuDiscussionRepository->findAllQuery($search);
        $contenu_discussions = $paginator->paginate(
            $queryBuilder->getQuery(),
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('admin/contenu_discussion/index.html.twig', [
            'contenu_discussions' => $contenu_discussions,
        ]);
    }

    #[Route('/new', name: 'app_admin_contenu_discussion_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, FileUploadService $fileUploadService): Response
    {
        $contenuDiscussion = new ContenuDiscussion();
        $form = $this->createForm(ContenuDiscussionType::class, $contenuDiscussion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $request->request->all()['contenu_discussion'];

            $formStatus = ContenuDiscussion::STATUT_DRAFT;
            if (isset($formData['publish_button'])) {
                $formStatus = ContenuDiscussion::STATUT_PUBLISHED;
            }

            $contenuDiscussion->setStatut($formStatus);

            $image = $form->get('image')->getData();
            if ($image) {
                $file = $fileUploadService->upload($image);
                $contenuDiscussion->setImage($file);
            }

            $media = $form->get('media')->getData();
            if ($media) {
                $file = $fileUploadService->upload($media);
                $contenuDiscussion->setMedia($file);
            }

            $entityManager->persist($contenuDiscussion);
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_contenu_discussion_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/contenu_discussion/new.html.twig', [
            'contenu_discussion' => $contenuDiscussion,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_admin_contenu_discussion_show', methods: ['GET'])]
    public function show(ContenuDiscussion $contenuDiscussion): Response
    {
        return $this->render('admin/contenu_discussion/show.html.twig', [
            'contenu_discussion' => $contenuDiscussion,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_contenu_discussion_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ContenuDiscussion $contenuDiscussion, EntityManagerInterface $entityManager, FileUploadService $fileUploadService): Response
    {
        $form = $this->createForm(ContenuDiscussionType::class, $contenuDiscussion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $request->request->all()['contenu_discussion'];

            $formStatus = ContenuDiscussion::STATUT_DRAFT;
            if (isset($formData['publish_button'])) {
                $formStatus = ContenuDiscussion::STATUT_PUBLISHED;
            }

            $contenuDiscussion->setStatut($formStatus);

            $image = $form->get('image')->getData();
            if ($image) {
                if ($contenuDiscussion->getImage()) {
                    $oldImage = $fileUploadService->delete($contenuDiscussion->getImage()->getName());
                    if ($oldImage) {
                        $entityManager->remove($oldImage);
                    }
                }
                $file = $fileUploadService->upload($image);
                $contenuDiscussion->setImage($file);
            }

            $media = $form->get('media')->getData();
            if ($media) {
                if ($contenuDiscussion->getMedia()) {
                    $oldMedia = $fileUploadService->delete($contenuDiscussion->getMedia()->getName());
                    if ($oldMedia) {
                        $entityManager->remove($oldMedia);
                    }
                }
                $file = $fileUploadService->upload($media);
                $contenuDiscussion->setMedia($file);
            }

            $entityManager->flush();

            return $this->redirectToRoute('app_admin_contenu_discussion_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/contenu_discussion/edit.html.twig', [
            'contenu_discussion' => $contenuDiscussion,
            'form' => $form->createView(),
            'image' => $contenuDiscussion->getImage(),
            'media' => $contenuDiscussion->getMedia(),
        ]);
    }

    #[Route('/{id}', name: 'app_admin_contenu_discussion_delete', methods: ['POST'])]
    public function delete(Request $request, ContenuDiscussion $contenuDiscussion, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$contenuDiscussion->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($contenuDiscussion);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_contenu_discussion_index', [], Response::HTTP_SEE_OTHER);
    }
}
