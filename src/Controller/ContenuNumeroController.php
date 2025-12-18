<?php

namespace App\Controller;

use App\Entity\ContenuNumero;
use App\Form\ContenuNumeroType;
use App\Repository\ContenuNumeroRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/contenu/numero')]
final class ContenuNumeroController extends AbstractController
{
    #[Route(name: 'app_contenu_numero_index', methods: ['GET'])]
    public function index(ContenuNumeroRepository $contenuNumeroRepository): Response
    {
        return $this->render('contenu_numero/index.html.twig', [
            'contenu_numeros' => $contenuNumeroRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_contenu_numero_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $contenuNumero = new ContenuNumero();
        $form = $this->createForm(ContenuNumeroType::class, $contenuNumero);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($contenuNumero);
            $entityManager->flush();

            return $this->redirectToRoute('app_contenu_numero_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('contenu_numero/new.html.twig', [
            'contenu_numero' => $contenuNumero,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_contenu_numero_show', methods: ['GET'])]
    public function show(ContenuNumero $contenuNumero): Response
    {
        return $this->render('contenu_numero/show.html.twig', [
            'contenu_numero' => $contenuNumero,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_contenu_numero_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ContenuNumero $contenuNumero, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ContenuNumeroType::class, $contenuNumero);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_contenu_numero_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('contenu_numero/edit.html.twig', [
            'contenu_numero' => $contenuNumero,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_contenu_numero_delete', methods: ['POST'])]
    public function delete(Request $request, ContenuNumero $contenuNumero, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$contenuNumero->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($contenuNumero);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_contenu_numero_index', [], Response::HTTP_SEE_OTHER);
    }
}
