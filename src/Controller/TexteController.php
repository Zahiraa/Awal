<?php

namespace App\Controller;

use App\Entity\Texte;
use App\Form\TexteType;
use App\Repository\TexteRepository;
use App\Service\FileUploadService;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


#[Route('/texte')]
final class TexteController extends AbstractController
{
    #[Route('', name: 'app_texte_index_front', methods: ['GET'])]
    public function index(TexteRepository $texteRepository): Response
    {
        $texteThisMonthOrLastMonth = $texteRepository->findTexteCurrentOrPreviousMonth();
   

        return $this->render('texte/indexFront.html.twig', [
            'textes' => $texteThisMonthOrLastMonth,
        ]);
    }


    #[Route('/{id}', name: 'app_texte_show', methods: ['GET'])]
    public function show(Texte $texte): Response
    {
        return $this->render('texte/showFront.html.twig', [
            'texte' => $texte,
        ]);
    }

 

   
}
