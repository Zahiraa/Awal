<?php

namespace App\Controller;

use App\Entity\Author;
use App\Form\AuthorType;
use App\Repository\AuthorRepository;
use App\Service\FileUploadService;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/author')]
final class AuthorController extends AbstractController
{
   
   #[Route('/{id}', name: 'app_author_show_front', methods: ['GET'])]
    public function show(Author $author): Response
    {
        return $this->render('author/showFront.html.twig', [
            'author' => $author,
        ]);
    }

}
