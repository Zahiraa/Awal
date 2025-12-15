<?php

namespace App\Controller\admin;

use App\Entity\Proposition;
use App\Form\PropositionForm;
use App\Manager\PropositionManager;
use App\Repository\PropositionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/proposition')]
final class AdminPropositionController extends AbstractController
{
    #[Route(name: 'app_admin_proposition_index', methods: ['GET'])]
    public function index(   Request $request,
                             PropositionRepository $propositionRepository,
                             PaginatorInterface $paginator): Response
    {
        $search = $request->query->get('search', '');
        $queryBuilder = $propositionRepository->findQueryAll($search);

        $propositions = $paginator->paginate(
            $queryBuilder->getQuery(),
            $request->query->getInt('page', 1),
            10
        );
        return $this->render('admin/admin_proposition/index.html.twig', [
            'propositions' =>$propositions,
        ]);
    }

    #[Route('/new', name: 'app_admin_proposition_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $proposition = new Proposition();
        $form = $this->createForm(PropositionForm::class, $proposition);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($proposition);
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_proposition_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/admin_proposition/new.html.twig', [
            'proposition' => $proposition,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_proposition_show', methods: ['GET'])]
    public function show(Proposition $proposition): Response
    {
        return $this->render('admin/admin_proposition/show.html.twig', [
            'proposition' => $proposition,
        ]);
    }
    // edit
    #[Route('/{id}/edit', name: 'app_admin_proposition_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Proposition $proposition, PropositionManager $propositionManager): Response
    {
        $form = $this->createForm(PropositionForm::class, $proposition);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $image = $form->get('image')->getData();

            $propositionManager->editProposition($proposition, $image);

            $this->addFlash("success",'La proposition a bien été modifiée.');

            return $this->redirectToRoute('app_admin_proposition_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/admin_proposition/edit.html.twig', [
            'proposition' => $proposition,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/action', name: 'app_admin_proposition_action', methods: ['GET', 'POST'])]
    public function action(Request $request, Proposition $proposition, PropositionManager $propositionManager , PropositionRepository $propositionRepository):Response
    {
        $action = $request->getPayload()->getString('action');
        $propositionManager->actionProposition($proposition, $action);
        return $this->redirectToRoute('app_admin_proposition_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}', name: 'app_admin_proposition_delete', methods: ['POST'])]
    public function delete(Request $request, Proposition $proposition, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$proposition->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($proposition);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_proposition_index', [], Response::HTTP_SEE_OTHER);
    }
}
