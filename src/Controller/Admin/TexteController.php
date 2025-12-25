<?php

namespace App\Controller\Admin;

use App\Entity\Texte;
use App\Form\TexteType;
use App\Repository\TexteRepository;
use App\Service\FileUploadService;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


#[Route('/admin/texte')]
final class TexteController extends AbstractController
{
    #[Route('', name: 'app_texte_index', methods: ['GET'])]
    public function index(Request $request, PaginatorInterface $paginator, TexteRepository $texteRepository): Response
    {
        $search = $request->query->get('search', '');
        $queryBuilder = $texteRepository->findAllQuery($search);
        $textes = $paginator->paginate(
            $queryBuilder->getQuery(),
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('texte/index.html.twig', [
            'textes' => $textes,
        ]);
    }

 

    #[Route('/new', name: 'app_texte_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, FileUploadService $fileUploadService): Response
    {
        $texte = new Texte();
        $form = $this->createForm(TexteType::class, $texte);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $request->request->all()['texte'];

            $formStatus = Texte::STATUT_DRAFT;
            // si le button "publish_button" a été cliqué pour submit, on met le $formStatus en statut "published"
            if (isset($formData['publish_button'])) {
                $formStatus = Texte::STATUT_PUBLISHED;
            }

            $texte->setStatut($formStatus);

            $image = $form->get('image')->getData();
            if ($image) {
                $file = $fileUploadService->upload($image);
                $texte->setImage($file);
            }

            $media = $form->get('media')->getData();
            if ($media) {
                $file = $fileUploadService->upload($media);
                $texte->setMedia($file);
            }

            $entityManager->persist($texte);
            $entityManager->flush();

            return $this->redirectToRoute('app_texte_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('texte/new.html.twig', [
            'texte' => $texte,
            'form' => $form,
            'image' => null,
            'media' => null,
        ]);
    }

    #[Route('/{id}', name: 'app_texte_show', methods: ['GET'])]
    public function show(Texte $texte): Response
    {
        return $this->render('texte/show.html.twig', [
            'texte' => $texte,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_texte_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Texte $texte, EntityManagerInterface $entityManager, FileUploadService $fileUploadService): Response
    {
        $form = $this->createForm(TexteType::class, $texte);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $request->request->all()['texte'];

            $formStatus = Texte::STATUT_DRAFT;
            // si le button "publish_button" a été cliqué pour submit, on met le $formStatus en statut "published"
            if (isset($formData['publish_button'])) {
                $formStatus = Texte::STATUT_PUBLISHED;
            }

            $texte->setStatut($formStatus);

            $image = $form->get('image')->getData();
            if ($image) {
                if ($texte->getImage()) {
                    $oldImage = $fileUploadService->delete($texte->getImage()->getName());
                    if ($oldImage) {
                        $entityManager->remove($oldImage);
                    }
                }
                $file = $fileUploadService->upload($image);
                $texte->setImage($file);
            }

            $media = $form->get('media')->getData();
            if ($media) {
                if ($texte->getMedia()) {
                    $oldMedia = $fileUploadService->delete($texte->getMedia()->getName());
                    if ($oldMedia) {
                        $entityManager->remove($oldMedia);
                    }
                }
                $file = $fileUploadService->upload($media);
                $texte->setMedia($file);
            }

            $entityManager->flush();

            return $this->redirectToRoute('app_texte_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('texte/edit.html.twig', [
            'texte' => $texte,
            'form' => $form,
            'image' => $texte->getImage(),
            'media' => $texte->getMedia(),
        ]);
    }

    #[Route('/{id}', name: 'app_texte_delete', methods: ['POST'])]
    public function delete(Request $request, Texte $texte, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$texte->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($texte);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_texte_index', [], Response::HTTP_SEE_OTHER);
    }
}
