<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleForm;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/articles')]
final class ArticleController extends AbstractController
{
    #[Route(name: 'article_index', methods: ['GET'])]
    public function index(Request $request,ArticleRepository $articleRepository,PaginatorInterface $paginator): Response
    {
        $queryBuilder = $articleRepository->findPublishedArticles();
        $articles  = $paginator->paginate(
            $queryBuilder->getQuery(),
            $request->query->getInt('page', 1),
            6
        );
        return $this->render('article/index.html.twig', [
            'articles' => $articles,
        ]);
    }


    #[Route('/{id}', name: 'article_show', methods: ['GET'])]
    public function show(int $id,ArticleRepository $articleRepository): Response
    {
        $article = $articleRepository->find($id);
        if (!$article || $article->getStatus() !== Article::STATUS_PUBLISHED) {
            return $this->redirectToRoute('article_index');
        }
        return $this->render('article/show.html.twig', [
            'article' => $article,
        ]);

    }

    // findPublishedArticlesByAuthor
    #[Route('/author/{id}', name: 'article_author', methods: ['GET'])]
    public function showByAuthor(int $id,ArticleRepository $articleRepository): Response
    {
        $articles = $articleRepository->findPublishedArticlesByAuthor($id)->orderBy(['createdAt' => 'DESC'])->getFirstResult();
        if (!$articles) {
            return $this->redirectToRoute('home');
        }
        return $this->render('article/index.html.twig', [
            'articles' => $articles,
        ]);
    }
    

}
