<?php

namespace App\Controller;

use App\Entity\Proposition;
use App\Entity\User;
use App\Form\PropositionForm;
use App\Manager\PropositionManager;
use App\Repository\PropositionRepository;
use App\Service\FileUploadService;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/mes-propositions')]
#[IsGranted('ROLE_USER')]
class PropositionController extends DefaultController
{
    public function __construct(private TranslatorInterface $translator)
    {
    }

    /**
     * Vérifie que l'utilisateur n'est pas admin ou manager
     */
    private function canAccess(?Proposition $proposition = null, ?string $actionType = null): bool
    {
        if ($this->isGranted('ROLE_ADMIN') || $this->isGranted('ROLE_MANAGER')) {
            $this->addWarningMessage('Les administrateurs et managers ne peuvent pas accéder à cette page.');
            return false;
        }
        if ($proposition !== null) {
            return $this->canAccessProposition($proposition, $actionType);
        }

        return true;
    }

    private function canAccessProposition(Proposition $proposition, ?string $actionType): bool
    {
        $status = $proposition->getStatut();

        if ($status === Proposition::STATUT_APPROVED  && $actionType === 'edit') {
            $this->addWarningMessage('Vous n\'avez pas les droits pour modifier cette proposition.');
            return false;
        }

        return true;
    }

    #[Route(name: 'app_proposition_index', methods: ['GET'])]
    public function index(
        Request $request,
        PropositionRepository $propositionRepository,
        PaginatorInterface $paginator
    ): Response {
        $canAccess = $this->canAccess();

        if (!$canAccess) {
            return $this->redirectToRoute('app_admin_proposition_index');
        }

        $user = $this->getUser();
        $search = $request->query->get('search', '');
        $queryBuilder = $propositionRepository->findQueryByUser($user, $search);

        $propositions = $paginator->paginate(
            $queryBuilder->getQuery(),
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('proposition/index.html.twig', [
            'propositions' => $propositions,
        ]);
    }

    /**
     * @throws TransportExceptionInterface
     */
    #[Route('/new', name: 'app_proposition_new', methods: ['GET', 'POST'])]
    public function new(Request $request, PropositionManager $propositionManager): Response
    {
        $canAccess = $this->canAccess();

        if (!$canAccess) {
            return $this->redirectToRoute('app_proposition_index');
        }

        $proposition = new Proposition();
        $form = $this->createForm(PropositionForm::class, $proposition);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $image = $form->get('image')->getData();
            if($proposition->getContents()->first()->getTitreFr() || $proposition->getContents()->first()->getTitreAr() || $proposition->getContents()->first()->getTitreDr()){
                $propositionManager->saveProposition($proposition, $image);
                $this->addSuccessMessage($this->translator->trans('common.flash.success.addProposition'));
                return $this->redirectToRoute('app_proposition_index', [], Response::HTTP_SEE_OTHER);
            }

        }

        return $this->render('proposition/new.html.twig', [
            'proposition' => $proposition,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_proposition_show', methods: ['GET'])]
    public function show(Proposition $proposition): Response
    {
        $canAccess = $this->canAccess($proposition);

        if (!$canAccess) {
            return $this->redirectToRoute('app_proposition_index');
        }

        return $this->render('proposition/show.html.twig', [
            'proposition' => $proposition,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_proposition_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Proposition $proposition, PropositionManager $propositionManager): Response
    {
        $canAccess = $this->canAccess($proposition, 'edit');

        if (!$canAccess) {
            return $this->redirectToRoute('app_proposition_index');
        }
        $form = $this->createForm(PropositionForm::class, $proposition);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $image = $form->get('image')->getData();

            $propositionManager->editProposition($proposition, $image);

            $this->addSuccessMessage($this->translator->trans('common.flash.success.updateProposition'));

            return $this->redirectToRoute('app_proposition_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('proposition/edit.html.twig', [
            'proposition' => $proposition,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_proposition_delete', methods: ['POST'])]
    public function delete(Request $request, Proposition $proposition, EntityManagerInterface $entityManager): Response
    {

        $canAccess = $this->canAccess($proposition, 'delete');

        if (!$canAccess) {
            return $this->redirectToRoute('app_proposition_index');
        }

        if ($this->isCsrfTokenValid('delete' . $proposition->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($proposition);
            $entityManager->flush();

            $this->addSuccessMessage($this->translator->trans('common.flash.success.deleteProposition.succes') .'');
        }

        return $this->redirectToRoute('app_proposition_index', [], Response::HTTP_SEE_OTHER);
    }
}
