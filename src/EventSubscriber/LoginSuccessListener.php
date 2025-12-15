<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\SecurityEvents;
use Symfony\Component\Mailer\MailerInterface; // Make sure to inject MailerInterface
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface; // Inject SessionInterface for flash messages
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use App\Entity\User; // Your User entity
use App\Security\EmailVerifier; // Assuming you have an EmailVerifier service

class LoginSuccessListener implements EventSubscriberInterface
{
    private $emailVerifier;
    private $mailerFromAddress;
    private $mailerFromName;
    private $session;

    public function __construct(
        EmailVerifier $emailVerifier,
        string $mailerFromAddress, // Inject from parameters.yaml or services.yaml
        string $mailerFromName,    // Inject from parameters.yaml or services.yaml
        RequestStack $requestStack
    ) {
        $this->emailVerifier = $emailVerifier;
        $this->mailerFromAddress = $mailerFromAddress;
        $this->mailerFromName = $mailerFromName;
        $this->requestStack = $requestStack;
    }

    public static function getSubscribedEvents()
    {
        return [
            // This event is fired after the user is authenticated (username/password checked)
            SecurityEvents::INTERACTIVE_LOGIN => 'onInteractiveLogin',
        ];
    }

    public function onInteractiveLogin(InteractiveLoginEvent $event)
    {
        /** @var User $user */
        $user = $event->getAuthenticationToken()->getUser();

        if (!$user instanceof User) {
            return; // Not your User entity, skip
        }

        if ($user->getStatut() !== User::ACTIVE || !$user->isVerified()) {
                $this->emailVerifier->sendEmailConfirmation(
                    'app_verify_email',
                    $user,
                    $this->mailerFromAddress,
                    $this->mailerFromName,
                    $user->getEmail(),
                    '(Compte pas encore activé) Veuillez confirmer votre email',
                    'emails/security/confirmation_email.html.twig'
                );

            // Always throw the exception if the account isn't active/verified
            throw new CustomUserMessageAccountStatusException('Activez votre compte via le lien envoyé par e-mail.');
        }
    }
}