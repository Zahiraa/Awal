<?php


namespace App\Controller;

use App\Entity\Terme;
use App\Repository\TermeRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class GlossaireController extends DefaultController
{
    public function __construct(private readonly UrlGeneratorInterface $urlGenerator)
    {
    }

    #[Route('/glossaire', name: 'app_glossaire_index', methods: ['GET'])]
    public function index(Request $request,TermeRepository $termeRepository,PaginatorInterface $paginator): Response
    {
        $startWith=$request->get("startWith", 'a');
        $page=$request->get("page", 1);
        $termes = $termeRepository->findByStartWithLetter($startWith);
        $formattedResults=[];

        foreach ($termes as $terme) {

            $formattedResults[] = [
                'id' => $terme->getId(),
                'french' => $terme->getContents()->first()->getTitreFr(),
                'arabic' => $terme->getContents()->first()->getTitreAr(),
                'darija' => $terme->getContents()->first()->getTitreDr(),
                'url' => $this->urlGenerator->generate('show_terme', ['id' => $terme->getId()]),
            ];
        }
        // Paginate the results
        $results = $paginator->paginate(
            $formattedResults,
            $request->query->getInt('page', $page), // page number
            50
        );
        return $this->render('glossaire/index.html.twig', [
            'results' => $results,
            'startWith' => $startWith,
        ]);
    }

    #[Route(path: 'glossaire/{id}', name: 'show_terme', methods: ['GET'])]
    public function showTerme(Terme $terme): Response
    {
        return $this->render('glossaire/show-terme.html.twig', [
            'terme' => $terme,
        ]);
    }
}