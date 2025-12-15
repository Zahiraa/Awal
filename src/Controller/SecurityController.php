<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\NewPasswordForm;
use App\Form\RegistrationForm;
use App\Form\ResetPasswordForm;
use App\Repository\UserRepository;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class SecurityController extends DefaultController
{

    public function __construct(private EmailVerifier $emailVerifier)
    {
    }

    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils, Request $request, TranslatorInterface $translator): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_home');
        }
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        if ($error) {
            $translatedMessage = $translator->trans($error->getMessageKey(), $error->getMessageData(), 'security');
            $this->addErrorMessage($translatedMessage);
        }

        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    /**
     * @Route("/register", name="app_register")
     *
     * @param Request $request
     * @param UserPasswordHasherInterface $userPasswordHasher
     * @param EntityManagerInterface $entityManager
     *
     * @return Response
     *
     * @throws VerifyEmailExceptionInterface
     */
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_home');
        }

        $user = new User();
        $form = $this->createForm(RegistrationForm::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string $plainPassword */
            $plainPassword = $form->get('plainPassword')->getData();
            $user->setRoles(['ROLE_USER']);
            $user->setStatut(User::INACTIVE);

            // encode the plain password
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));
            $entityManager->persist($user);
            $entityManager->flush();

            $this->emailVerifier->sendEmailConfirmation(
                'app_verify_email',
                $user,
                $this->getParameter('mailer_from_address'),
                $this->getParameter('mailer_from_name'),
                $user->getEmail(),
                'Veuillez confirmer votre email',
                'emails/security/confirmation_email.html.twig'
            );
            return $this->redirectToRoute('registration_check_email', [
                'email' => $user->getEmail(),
            ]);
        }

        return $this->render('security/registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }

    /**
     * Display a message to the user after registration.
     *
     * @return Response
     */
    #[Route('/registration/check-email', name: 'registration_check_email')]
    public function checkEmail(Request $request): Response
    {
        $email = $request->query->get('email');
        return $this->render('security/registration/mail-send.html.twig',
            [
                'email' => $email,
            ]
        );
    }

    /**
     * Verification of an email address.
     *
     * @param Request $request
     * @param TranslatorInterface $translator
     * @param UserRepository $userRepository
     * @return Response
     */
    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request, TranslatorInterface $translator, UserRepository $userRepository): Response
    {
        $id = $request->query->get('id');
        $user = $userRepository->find($id);

        if (null === $id || null === $user) {
            return $this->redirectToRoute('app_register');
        }

        $verification = $this->emailVerifier->handleEmailConfirmation($request, $user);
        if ($verification['status'] === "success") {
            $this->addSuccessMessage($translator->trans('register.verify.successMessage'));
            return $this->redirectToRoute('app_login');
        }
        if ($verification['status'] === "error") {
            $this->addErrorMessage($verification['message']);
        }
        return $this->redirectToRoute('app_register');
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route('/', name: 'app_home')]
    public function homepage(): Response
    {
        return $this->render('home/index.html.twig');
    }
}
