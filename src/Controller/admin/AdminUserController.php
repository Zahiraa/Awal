<?php

namespace App\Controller\Admin;

use App\Controller\DefaultController;
use App\Entity\User;
use App\Form\AdminUserForm;
use App\Form\InvitationFormType;
use App\Form\UserForm;
use App\Manager\UserManager;
use App\Repository\UserRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/user')]
final class AdminUserController extends DefaultController
{
    // add constructor to inject UserRepository and UserManager
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    #[Route(name: 'app_admin_user_index', methods: ['GET'])]
    public function index(Request $request, PaginatorInterface $paginator): Response
    {
        $this->checkAdmin();
        $search = $request->query->get('search', '');
        $queryBuilder = $this->userRepository->findAllUsers($search);

        $users = $paginator->paginate(
            $queryBuilder->getQuery(),
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('admin/admin_user/index.html.twig', [
            'users' => $users,
        ]);
    }

    public function checkAdmin()
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            $this->addFlash('error', 'Vous n\'avez pas les droits nécessaires pour accéder à cette page.');
            return $this->redirectToRoute('app_admin_user_show', ['id' => $this->getUser()->getId()]);
        }
    }

    #[Route('/invitations', name: 'app_admin_user_invite', methods: ['GET', 'POST'])]
    public function invite(Request $request, UserManager $userManager): Response
    {
        $this->checkAdmin();

        $form = $this->createForm(InvitationFormType::class, ['invitations' => [['email' => '', 'role' => 'ROLE_MANAGER']]]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $invitations = $userManager->inviteNewUsers($data['invitations'] ?? []);

            foreach ($invitations as $invitation) {
                if ($invitation['status'] === "success") {
                    $this->addSuccessMessage($invitation['message']);
                } else {
                    $this->addWarningMessage($invitation['message']);
                }
            }
            return $this->redirectToRoute('app_admin_user_invite');
        }

        return $this->render('admin/admin_user/invite.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_admin_user_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('admin/admin_user/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {

        $this->checkAccountRole($user);
        $form = $this->createForm(AdminUserForm::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            if ($this->isGranted('ROLE_ADMIN')) {
                $this->addFlash('success', 'L\'utilisateur a été mis à jour avec succès.');
                return $this->redirectToRoute('app_admin_user_index', [], Response::HTTP_SEE_OTHER);
            }

            // if not admin, redirect to user profile
            $this->addFlash('success', 'Votre profil a été mis à jour avec succès.');

            return $this->redirectToRoute('app_admin_user_show', ['id' => $user->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/admin_user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    public function checkAccountRole($user)
    {
        if (!$this->isGranted('ROLE_ADMIN') && $user->getId() !== $this->getUser()->getId()) {
            $this->addFlash('error', 'Vous n\'avez pas les droits nécessaires pour accéder à cette page.');
            return $this->redirectToRoute('app_admin_user_show', ['id' => $this->getUser()->getId()]);
        }
    }

    #[Route('/{id}', name: 'app_admin_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $this->checkAccountRole($user);
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->getPayload()->getString('_token'))) {
            $user->setDeletedAt(new DateTime());
            $user->setEmail($user->getEmail() . '_deleted_' . (new DateTime())->format('YmdHi'));
            $user->setStatut(User::DELETED);
            $user->setIsVerified(0);
            $user->setRegistrationToken(null);
            $entityManager->flush();
            $this->addFlash('success', 'L\'utilisateur a été supprimé avec succès.');
        }

        return $this->redirectToRoute('app_admin_user_index');
    }
}
