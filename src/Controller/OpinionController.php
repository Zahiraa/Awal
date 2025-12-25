<?php

namespace App\Controller;

use App\Entity\Opinion;
use App\Repository\OpinionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/opinion')]
final class OpinionController extends AbstractController
{


    #[Route('/{id}', name: 'app_opinion_show_front', methods: ['GET'])]
    public function show(Opinion $opinion): Response
    {
        return $this->render('opinion/showFront.html.twig', [
            'opinion' => $opinion,
        ]);
    }

}
