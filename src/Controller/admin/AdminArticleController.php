<?php

namespace App\Controller\admin;

use App\Controller\DefaultController;
use App\Entity\Article;
use App\Entity\Terme;
use App\Entity\User;
use App\Form\ArticleForm;
use App\Manager\ArticleManager;
use App\Manager\TermeManager;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/article')]
final class AdminArticleController extends DefaultController
{
    #[Route(name: 'app_admin_article_index', methods: ['GET'])]
    public function index(Request $request, PaginatorInterface $paginator, ArticleRepository $articleRepository): Response
    {
        $search = $request->query->get('search', '');
        $queryBuilder = $articleRepository->findAllQuery($search);

        $articles = $paginator->paginate(
            $queryBuilder->getQuery(),
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('admin/admin_article/index.html.twig', [
            'articles' => $articles,
        ]);
    }

    /**
     * @throws TransportExceptionInterface
     */
    #[Route('/new', name: 'app_admin_article_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ArticleManager $articleManager): Response
    {
        $article = new Article();
        $form = $this->createForm(ArticleForm::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $request->request->all()['article_form'];

            $formStatus = 'drafted';
            // si le button "draft_button" a été cliquer pour submit, on met le $formStatus en statut "draft"
            if (isset($formData['publish_button'])) {
                $formStatus = 'published';
            }

            $image = $form->get('image')->getData();
            $media = $form->get('media')->getData();
            $articleManager->saveArticle($article, $formStatus, $image, false, null, $media);

            $this->addSuccessMessage('Votre article a bien été ajoutée.');

            return $this->redirectToRoute('app_admin_article_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/admin_article/new.html.twig', [
            'article' => $article,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_article_show', methods: ['GET'])]
    public function show(Article $article): Response
    {
        return $this->render('admin/admin_article/show.html.twig', [
            'article' => $article,
        ]);
    }

    /**
     * @throws TransportExceptionInterface
     */
    #[Route('/{id}/edit', name: 'app_admin_article_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Article $article, ArticleManager $articleManager): Response
    {
        $form = $this->createForm(ArticleForm::class, $article);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $request->request->all()['article_form'];

            $formStatus = 'drafted';
            // si le button "draft_button" a été cliquer pour submit, on met le $formStatus en statut "draft"
            if (isset($formData['publish_button'])) {
                $formStatus = 'published';
            }

            $image = $form->get('image')->getData();
            $media = $form->get('media')->getData();
            $articleManager->saveArticle($article, $formStatus, $image, true, $media);

            $this->addSuccessMessage('Votre article a bien été modifiée.');


            return $this->redirectToRoute('app_admin_article_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/admin_article/edit.html.twig', [
            'article' => $article,
            'form' => $form,
            'image' => $article->getImage(),
            'media' => $article->getMedia(),
        ]);
    }



    #[Route('/{id}', name: 'app_admin_article_delete', methods: ['POST'])]
    public function delete(Request $request, Article $article, EntityManagerInterface $entityManager ): Response
    {
        /** @var User $me */
        $me = $this->getUser();


        if ($this->isCsrfTokenValid('delete'.$article->getId(), $request->getPayload()->getString('_token'))) {
            $article->setImage(null);
            $entityManager->remove($article);
            $entityManager->flush();

            $this->addSuccessMessage('L\'article a bien été supprimé.');
        }

        return $this->redirectToRoute('app_admin_article_index', [], Response::HTTP_SEE_OTHER);
    }
}
