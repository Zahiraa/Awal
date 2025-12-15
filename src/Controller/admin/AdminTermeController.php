<?php

namespace App\Controller\admin;

use App\Controller\DefaultController;
use App\Entity\Terme;
use App\Form\TermeForm;
use App\Manager\PropositionManager;
use App\Manager\TermeManager;
use App\Repository\TermeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/terme')]
final class AdminTermeController extends DefaultController
{


    public function __construct(private TermeRepository $termeRepository,)
    {

    }

    #[Route(name: 'app_admin_terme_index', methods: ['GET'])]
    public function index( Request $request, PaginatorInterface $paginator): Response
    {

        $search = $request->query->get('search', '');
        $queryBuilder = $this->termeRepository->findAllQuery($search);

        $termes = $paginator->paginate(
            $queryBuilder->getQuery(),
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('admin/admin_terme/index.html.twig', [
            'termes' => $termes,
        ]);
    }


    #[Route('/new', name: 'app_admin_terme_new', methods: ['GET', 'POST'])]
    public function new(Request $request, TermeManager $termeManager): Response
    {
        $terme = new Terme();
        $form = $this->createForm(TermeForm::class, $terme);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (count($terme->getContents()) === 0) {
                $this->addErrorMessage('Veuillez ajouter au moins un contenu');
                return $this->redirectToRoute('app_admin_terme_new');
            }

            $formData = $request->request->all()['terme_form'];

            $formStatus = Terme::STATUT_DRAFT;
            // si le button "draft_button" a été cliquer pour submit, on met le $formStatus en statut "draft"
            if (isset($formData['publish_button'])) {
                $formStatus =Terme::STATUT_PUBLISHED;
            }
            $image = $form->get('image')->getData();
            $termeManager->saveTerme($terme, $formStatus, $image);
            $this->addMessage($terme->getStatut());

            return $this->redirectToRoute('app_admin_terme_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/admin_terme/new.html.twig', [
            'terme' => $terme,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_admin_terme_show', methods: ['GET'])]
    public function show(Terme $terme): Response
    {
        return $this->render('admin/admin_terme/show.html.twig', [
            'terme' => $terme,
        ]);
    }

    private function addMessage($statut): void
    {
        switch ($statut) {
            case Terme::STATUT_DRAFT:
                $this->addInfoMessage('Le terme a été enregistré en brouillon !');
                break;
            case Terme::STATUT_WAITING_APPROVAL:
                $this->addWarningMessage('Le terme a été enregistré et en attente d\'approbation !');
                break;
            case Terme::STATUT_PUBLISHED:
                $this->addSuccessMessage('Le terme a été publier !');
                break;
        }
    }

    #[Route('/{id}/edit', name: 'app_admin_terme_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Terme $terme, TermeManager $termeManager): Response
    {
        $form = $this->createForm(TermeForm::class, $terme);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (count($terme->getContents()) === 0) {
                $this->addErrorMessage('Veuillez ajouter au moins un contenu');
                return $this->redirectToRoute('app_admin_terme_new');
            }

            $formData = $request->request->all()['terme_form'];
            $formStatus =  Terme::STATUT_DRAFT;

            // si le button "draft_button" a été cliquer pour submit, on met le $formStatus en statut "draft"
            if (isset($formData['publish_button'])) {
                $formStatus =  Terme::STATUT_PUBLISHED;
            }

            $image = $form->get('image')->getData();
            $termeManager->editTerme($terme, $formStatus, $image);
            $this->addMessage($terme->getStatut());

            return $this->redirectToRoute('app_admin_terme_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/admin_terme/edit.html.twig', [
            'terme' => $terme,
            'form' => $form->createView(),
            'image' => $terme->getImage(),
        ]);
    }

    #[Route('/{id}/action', name: 'app_admin_terme_action', methods: ['GET', 'POST'])]
    public function action(Terme $terme, TermeManager $termeManager):Response
    {
        $termeManager->approveTerme($terme);
        return $this->redirectToRoute('app_admin_terme_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}', name: 'app_admin_terme_delete', methods: ['POST'])]
    public function delete(Request $request, Terme $terme, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $terme->getId(), $request->getPayload()->getString('_token'))) {
            $terme->setImage(null);
            $entityManager->remove($terme);
            $entityManager->flush();

            $this->addSuccessMessage('Le terme a bien été supprimé.');
        }

        return $this->redirectToRoute('app_admin_terme_index', [], Response::HTTP_SEE_OTHER);
    }
}
