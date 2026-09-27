<?php

namespace App\Controller\Admin;

use App\Entity\Podcast;
use App\Form\PodcastType;
use App\Repository\PodcastRepository;
use App\Service\FileUploadService;
use Doctrine\ORM\EntityManagerInterface;
use App\Controller\DefaultController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/podcast')]

final class AdminPodcastController extends DefaultController
{
    #[Route(name: 'app_admin_podcast_index', methods: ['GET'])]
    public function index(PodcastRepository $podcastRepository): Response
    {
        return $this->render('podcast/admin/index.html.twig', [
            'podcasts' => $podcastRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_admin_podcast_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager,FileUploadService $fileUploadService): Response
    {
        $podcast = new Podcast();
        $form = $this->createForm(PodcastType::class, $podcast);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
             $image = $form->get('image')->getData();
            
                  if ($image) {
                $file = $fileUploadService->upload($image);
                $podcast->setImage($file);
            }
            
            $media = $form->get('media')->getData();
            if ($media) {
                $file = $fileUploadService->upload($media);
                $podcast->setMedia($file);
            }
            $entityManager->persist($podcast);
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_podcast_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('podcast/admin/new.html.twig', [
            'podcast' => $podcast,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_podcast_show', methods: ['GET'])]
    public function show(Podcast $podcast): Response
    {
        return $this->render('podcast/admin/show.html.twig', [
            'podcast' => $podcast,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_podcast_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Podcast $podcast, EntityManagerInterface $entityManager,FileUploadService $fileUploadService): Response
    {
        $form = $this->createForm(PodcastType::class, $podcast);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
             $image = $form->get('image')->getData();
               if ($image) {
                $file = $fileUploadService->upload($image);
                $podcast->setImage($file);
            }
            
            $media = $form->get('media')->getData();
            if ($media) {
                $file = $fileUploadService->upload($media);
                $podcast->setMedia($file);
            }
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_podcast_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('podcast/admin/edit.html.twig', [
            'podcast' => $podcast,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_podcast_delete', methods: ['POST'])]
    public function delete(Request $request, Podcast $podcast, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$podcast->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($podcast);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_podcast_index', [], Response::HTTP_SEE_OTHER);
    }
}
