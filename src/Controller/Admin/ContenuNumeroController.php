<?php

namespace App\Controller\Admin;

use App\Entity\ContenuNumero;
use App\Form\ContenuNumeroType;
use App\Repository\ContenuNumeroRepository;
use App\Service\FileUploadService;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/contenu-numero')]
final class ContenuNumeroController extends AbstractController
{
    #[Route('', name: 'app_admin_contenu_numero_index', methods: ['GET'])]
    public function index(Request $request, PaginatorInterface $paginator,ContenuNumeroRepository $contenuNumeroRepository): Response
    {
        $search = $request->query->get('search', '');
        $queryBuilder = $contenuNumeroRepository->findAllQuery($search);
        $contenu_numeros = $paginator->paginate(
            $queryBuilder->getQuery(),
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('admin/contenu_numero/index.html.twig', [
            'contenu_numeros' => $contenu_numeros,
        ]);
    }

    #[Route('/new', name: 'app_admin_contenu_numero_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, FileUploadService $fileUploadService): Response
    {
        $contenuNumero = new ContenuNumero();
        $form = $this->createForm(ContenuNumeroType::class, $contenuNumero);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $request->request->all()['contenu_numero'];

            $formStatus = ContenuNumero::STATUT_DRAFT;
            // si le button "publish_button" a été cliqué pour submit, on met le $formStatus en statut "published"
            if (isset($formData['publish_button'])) {
                $formStatus = ContenuNumero::STATUT_PUBLISHED;
            }

            $contenuNumero->setStatut($formStatus);

            $image = $form->get('image')->getData();
            if ($image) {
                $file = $fileUploadService->upload($image);
                $contenuNumero->setImage($file);
            }

            $entityManager->persist($contenuNumero);
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_contenu_numero_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/contenu_numero/new.html.twig', [
            'contenu_numero' => $contenuNumero,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_contenu_numero_show', methods: ['GET'])]
    public function show(ContenuNumero $contenuNumero): Response
    {
        return $this->render('admin/contenu_numero/show.html.twig', [
            'contenu_numero' => $contenuNumero,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_contenu_numero_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ContenuNumero $contenuNumero, EntityManagerInterface $entityManager, FileUploadService $fileUploadService): Response
    {
        $form = $this->createForm(ContenuNumeroType::class, $contenuNumero);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $request->request->all()['contenu_numero'];

            $formStatus = ContenuNumero::STATUT_DRAFT;
            // si le button "publish_button" a été cliqué pour submit, on met le $formStatus en statut "published"
            if (isset($formData['publish_button'])) {
                $formStatus = ContenuNumero::STATUT_PUBLISHED;
            }

            $contenuNumero->setStatut($formStatus);

            $image = $form->get('image')->getData();
            if ($image) {
                if ($contenuNumero->getImage()) {
                    $oldImage = $fileUploadService->delete($contenuNumero->getImage()->getName());
                    if ($oldImage) {
                        $entityManager->remove($oldImage);
                    }
                }
                $file = $fileUploadService->upload($image);
                $contenuNumero->setImage($file);
            }

            $entityManager->flush();

            return $this->redirectToRoute('app_admin_contenu_numero_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/contenu_numero/edit.html.twig', [
            'contenu_numero' => $contenuNumero,
            'form' => $form,
            'image' => $contenuNumero->getImage(),
        ]);
    }

    #[Route('/{id}', name: 'app_admin_contenu_numero_delete', methods: ['POST'])]
    public function delete(Request $request, ContenuNumero $contenuNumero, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$contenuNumero->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($contenuNumero);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_contenu_numero_index', [], Response::HTTP_SEE_OTHER);
    }
}