<?php

namespace App\Controller;

use App\Entity\Contenu;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/contenu-numero')]
class ContenuNumeroController extends AbstractController
{
    #[Route('/contenu/{id}', name: 'app_contenu_numero_list', methods: ['GET'])]
    public function index(Contenu $contenu): Response
    {
        // affichage de tous les ContenuNumero d un contenu
        return $this->render('contenu_numero/index.html.twig', [
            'contenu' => $contenu,
            'contenu_numeros' => $contenu->getContenuNumeros()->filter(function($cn) {
                return $cn->getStatut() === \App\Entity\ContenuNumero::STATUT_PUBLISHED;
            }),
        ]);
    }

    #[Route('/{id}/show', name: 'app_contenu_numero_show', methods: ['GET'])]
    public function show(ContenuNumero $contenuNumero): Response
    {
        // affichage d un ContenuNumero
        return $this->render('contenu_numero/show.html.twig', [
            'contenuNumero' => $contenuNumero,
        ]);
    }
}