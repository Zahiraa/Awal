<?php

namespace App\Controller\Admin;

use App\Entity\Contenu;
use App\Form\ContenuType;
use App\Repository\ContenuRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/contenu')]
final class ContenuController extends AbstractController
{
    #[Route(name: 'app_contenu_index', methods: ['GET'])]
    public function index(ContenuRepository $contenuRepository): Response
    {
        return $this->render('admin/contenu/index.html.twig', [
            'contenus' => $contenuRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_contenu_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $contenu = new Contenu();
        $form = $this->createForm(ContenuType::class, $contenu);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
             $formData = $request->request->all()['contenu'];

            $formStatus = Contenu::STATUT_DRAFT;
            // si le button "publish_button" a été cliqué pour submit, on met le $formStatus en statut "published"
            if (isset($formData['publish_button'])) {
                $formStatus = Contenu::STATUT_PUBLISHED;
            }

            $contenu->setStatut($formStatus);
            $entityManager->persist($contenu);
            $entityManager->flush();

            return $this->redirectToRoute('app_contenu_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/contenu/new.html.twig', [
            'contenu' => $contenu,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_contenu_show', methods: ['GET'])]
    public function show(Contenu $contenu): Response
    {
        return $this->render('admin/contenu/show.html.twig', [
            'contenu' => $contenu,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_contenu_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Contenu $contenu, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ContenuType::class, $contenu);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $request->request->all()['contenu'];

            $formStatus = Contenu::STATUT_DRAFT;
            // si le button "publish_button" a été cliqué pour submit, on met le $formStatus en statut "published"
            if (isset($formData['publish_button'])) {
                $formStatus = Contenu::STATUT_PUBLISHED;
            }

            $contenu->setStatut($formStatus);   
            $entityManager->flush();

            return $this->redirectToRoute('app_contenu_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/contenu/edit.html.twig', [
            'contenu' => $contenu,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_contenu_delete', methods: ['POST'])]
    public function delete(Request $request, Contenu $contenu, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$contenu->getId(), $request->getPayload()->getString('_token'))) {
            foreach ($contenu->getContenuNumeros() as $cn) {
                $cn->setContenu(null); // casse la relation
            }
            foreach ($contenu->getContenuDiscussions() as $cd) {
                $cd->setContenu(null); // casse la relation
            }
            $entityManager->remove($contenu);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_contenu_index', [], Response::HTTP_SEE_OTHER);
    }
}
