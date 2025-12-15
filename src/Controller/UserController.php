<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserProfileForm;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/user')]
final class UserController extends AbstractController
{
    #[Route('/{id}', name: 'app_user_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        if(!$this->getUser()) {
            return $this->redirectToRoute('home');
        }
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager,TranslatorInterface $translator): Response
    {
        if(!$this->getUser()) {
            return $this->redirectToRoute('home');
        }
        $id=$request->get('id');
        $connectedUser = $this->getUser();
        if (!$user || $connectedUser->getId() != $id) {
            return $this->redirectToRoute('app_user_edit', ['id' => $connectedUser->getId()]);
        }
        $form = $this->createForm(UserProfileForm::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash("success", $translator->trans('common.flash.success.updateProfile') );
            return $this->redirectToRoute('app_user_show', ['id' => $user->getId()]);
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }




}
