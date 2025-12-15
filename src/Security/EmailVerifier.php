<?php

namespace App\Security;

use App\Entity\User;
use App\Manager\Mailler;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mailer\Messenger\SendEmailMessage;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Mime\Address;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;

class EmailVerifier
{
    public function __construct(
        private VerifyEmailHelperInterface $verifyEmailHelper,
        private MailerInterface $mailer,
        private Mailler $mailler,
        private EntityManagerInterface $entityManager,
        private ParameterBagInterface $parameterBag,
    ) {
    }

    public function sendEmailConfirmation(string $verifyEmailRouteName, User $user, $senderAdress,$senderName,$to,$subject,$html): void
    {
        $signatureComponents = $this->verifyEmailHelper->generateSignature(
            $verifyEmailRouteName,
            (string) $user->getId(),
            (string) $user->getEmail(),
            ['id' => $user->getId()]
        );
        $template= (new TemplatedEmail())
            ->from(new Address($senderAdress, $senderName))
            ->to((string)$to)
            ->subject($subject)
            ->htmlTemplate($html);
        $context = $template->getContext();
        $context['signedUrl'] = $signatureComponents->getSignedUrl();
        $context['expiresAtMessageKey'] = $signatureComponents->getExpirationMessageKey();
        $context['expiresAtMessageData'] = $signatureComponents->getExpirationMessageData();

        $template->context($context);
        $this->mailer->send($template);
    }
    /**
     * @throws VerifyEmailExceptionInterface
     */
    public function handleEmailConfirmation(Request $request, User $user)
    {
     $status="success";
     $message="";
        try{
            $this->verifyEmailHelper->validateEmailConfirmationFromRequest($request, (string) $user->getId(), (string) $user->getEmail());

            $user->setIsVerified(true);
            $user->setStatut(User::ACTIVE);

            $this->entityManager->persist($user);
            $this->entityManager->flush();
        }
        catch (VerifyEmailExceptionInterface $e) {
          $status="error";
            if (str_contains($e->getReason(), "expired")){
                $message = "Le lien de confirmation a expiré. Veuillez verifier votre email à nouveau.";
                $this->sendEmailConfirmation(
                    'app_verify_email',
                    $user,
                    $this->parameterBag->get('mailer_from_address'),$this->parameterBag->get('mailer_from_name'),
                    $user->getEmail(),
                    'Veuillez confirmer votre email',
                    'emails/security/confirmation_email.html.twig'
                );
            }
            else{
                $message= "Une erreur est survenue lors de la confirmation de votre email. Veuillez réessayer.";
            }
        }
        return ['status'=>$status,'message'=>$message];
    }
}
