<?php

namespace App\Controller;

use App\Entity\Contenu;
use App\Entity\ContenuNumero;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/contenu-numero')]
class ContenuNumeroController extends AbstractController
{
    #[Route('/contenu_archive/{id}', name: 'app_contenu_numero_archive_list', methods: ['GET'])]
    public function indexArchive(Contenu $contenu, \App\Repository\TexteRepository $texteRepository, \App\Repository\ArticleRepository $articleRepository): Response
    {
        // Fetch matching textes and articles by month/year of the Contenu's createdAt date
        $date = $contenu->getCreatedAt();
        $startOfMonth = (clone $date)->modify('first day of this month')->setTime(0, 0, 0);
        $endOfMonth = (clone $date)->modify('last day of this month')->setTime(23, 59, 59);

        $textes = $texteRepository->createQueryBuilder('t')
            ->where('t.statut = :status')
            ->andWhere('t.createdAt BETWEEN :start AND :end')
            ->setParameter('status', \App\Entity\Texte::STATUT_PUBLISHED)
            ->setParameter('start', $startOfMonth)
            ->setParameter('end', $endOfMonth)
            ->orderBy('t.createdAt', 'DESC')
            ->getQuery()
            ->getResult();

        $articles = $articleRepository->createQueryBuilder('art')
            ->where('art.statut = :status')
            ->andWhere('art.createdAt BETWEEN :start AND :end')
            ->setParameter('status', \App\Entity\Article::STATUT_PUBLISHED)
            ->setParameter('start', $startOfMonth)
            ->setParameter('end', $endOfMonth)
            ->orderBy('art.createdAt', 'DESC')
            ->getQuery()
            ->getResult();

        // affichage de tous les ContenuNumero d un contenu
        return $this->render('contenu_numero/archive.html.twig', [
            'contenu' => $contenu,
            'contenu_numeros' => $contenu->getContenuNumeros()->filter(function($cn) {
                return $cn->getStatut() === \App\Entity\ContenuNumero::STATUT_PUBLISHED;
            }),
            'contenu_discussions' => $contenu->getContenuDiscussions()->filter(function($cd) {
                return $cd->getStatut() === \App\Entity\ContenuDiscussion::STATUT_PUBLISHED;
            }),
            'textes' => $textes,
            'articles' => $articles,
        ]);
    }
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