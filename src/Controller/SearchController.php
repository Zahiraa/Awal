<?php


namespace App\Controller;

use App\Repository\TermeRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class SearchController extends DefaultController
{
    public function __construct(private readonly UrlGeneratorInterface $urlGenerator)
    {

    }
    #[Route(path: '/search', name: 'search_terms')]
    public function searchTerms(Request $request, TermeRepository $termeRepository): Response
    {
        $query = $request->query->get('terme');

        if (strlen($query) < 2) {
            return $this->render('search/index.html.twig' , [
                'results' => [],
                'terme' => $query,
            ]);
        }

        $results = $termeRepository->findAllPublished($query);
        $formattedResults = [];

        foreach ($results as $result) {

            $imageUrl = $result->getImage() ? $result->getImage()->getName() : null;

            $formattedResults[] = [
                'id' => $result->getId(),
                'french' => $result->getContents()->first()->getTitreFr(),
                'arabic' => $result->getContents()->first()->getTitreAr(),
                'darija' => $result->getContents()->first()->getTitreDr(),
                'url' => $this->urlGenerator->generate('show_terme', ['id' => $result->getId()]),
                'image' => '/uploads/' . $imageUrl
            ];
        }
        return $this->render('search/index.html.twig' , [
            'results' => $formattedResults,
            'terme' => $query,
        ]);
    }

    #[Route(path: '/api/search', name: 'api_search', methods: ['GET'])]
    public function search(Request $request, TermeRepository $termeRepository): JsonResponse
    {
        $query = $request->query->get('q', '');

        $results = $termeRepository->findAllPublished($query);
        $formattedResults = [];

        foreach ($results as $result) {

            $imageUrl = $result->getImage() ? $result->getImage()->getName() : null;

            $formattedResults[] = [
                'id' => $result->getId(),
                'french' => $result->getContents()->first()->getTitreFr(),
                'arabic' => $result->getContents()->first()->getTitreAr(),
                'darija' => $result->getContents()->first()->getTitreDr(),
                'url' => '/glossaire/' . $result->getId(),
                'image' => '/uploads/' . $imageUrl
            ];
        }

        return new JsonResponse($formattedResults);
    }


}