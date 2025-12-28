<?php

namespace App\Controller;

use App\Entity\ContenuDiscussion;
use App\Form\ContenuDiscussionType;
use App\Repository\ContenuDiscussionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/contenu/discussion')]
final class ContenuDiscussionController extends AbstractController
{
 

    #[Route('/{id}', name: 'app_contenu_discussion_show', methods: ['GET'])]
    public function show(ContenuDiscussion $contenuDiscussion): Response
    {
        return $this->render('contenu_discussion/show.html.twig', [
            'contenu_discussion' => $contenuDiscussion,
        ]);
    }


}
