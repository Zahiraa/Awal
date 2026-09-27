<?php

namespace App\Controller;

use App\Entity\Podcast;
use App\Form\PodcastType;
use App\Repository\PodcastRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/podcast')]
final class PodcastController extends AbstractController
{
    #[Route(name: 'app_podcast_index', methods: ['GET'])]
    public function index(PodcastRepository $podcastRepository): Response
    {
        return $this->render('podcast/index.html.twig', [
            // podcasts ordered by date descending and status published only
            'podcasts' => $podcastRepository->findBy(['statut' => Podcast::STATUT_PUBLISHED], ['createdAt' => 'DESC']),
        ]);
    }


    #[Route('/{id}', name: 'app_podcast_show', methods: ['GET'])]
    public function show(Podcast $podcast): Response
    {
        return $this->render('podcast/show.html.twig', [
            'podcast' => $podcast,
        ]);
    }



}
