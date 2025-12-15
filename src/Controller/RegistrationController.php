<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\CompleteRegistrationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Manager\UserManager;

class RegistrationController extends DefaultController
{
    #[Route('/complete-registration/{token}', name: 'app_complete_registration')]
    public function completeRegistration(string $token, Request $request, EntityManagerInterface $entityManager, UserManager $userManager): Response
    {
        $user = $entityManager->getRepository(User::class)->findOneBy(['registrationToken' => $token]);

        $originalEmail = $user->getEmail();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        // Vérifier si le token n'a pas expiré (7j)
        if ($user->getInvitedAt() < new \DateTime('-7 days')) {
            $this->addErrorMessage('Le lien d\'invitation est invalide ou a déjà été utilisé.');
            return $this->redirectToRoute('app_login');
        }

        $form = $this->createForm(CompleteRegistrationType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user->setEmail($originalEmail);
            $result = $userManager->completeRegistration(
                $user,
                [
                    'plainPassword' => $form->get('plainPassword')->getData(),
                    'nom' => $form->get('nom')->getData(),
                    'prenom' => $form->get('prenom')->getData(),
                ],
            );

            if ($result['success']) {
                $this->addSuccessMessage($result['message']);
            } else {
                $this->addErrorMessage($result['message']);
            }

            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/complete_registration.html.twig', [
            'form' => $form,
            'user' => $user
        ]);
    }
} 