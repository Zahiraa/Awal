<?php

namespace App\Controller\Admin;

use App\Entity\Texte;
use App\Form\TexteType;
use App\Repository\TexteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/texte')]
final class TexteController extends AbstractController
{
    #[Route(name: 'app_texte_index', methods: ['GET'])]
    public function index(TexteRepository $texteRepository): Response
    {
        return $this->render('texte/index.html.twig', [
            'textes' => $texteRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_texte_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $texte = new Texte();
        $form = $this->createForm(TexteType::class, $texte);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($texte);
            $entityManager->flush();

            return $this->redirectToRoute('app_texte_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('texte/new.html.twig', [
            'texte' => $texte,
            'form' => $form,
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
    public function edit(Request $request, Texte $texte, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TexteType::class, $texte);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_texte_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('texte/edit.html.twig', [
            'texte' => $texte,
            'form' => $form,
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
