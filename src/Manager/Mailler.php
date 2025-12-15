<?php

namespace App\Manager;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Contracts\Translation\TranslatorInterface;

class Mailler
{

    public function __construct(
        private readonly MailerInterface       $mailer,
        private readonly ParameterBagInterface $parameterBag,
         private readonly TranslatorInterface $translator
    )
    {
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function sendTemplateEmail(string $to, string $subject, string $templatePath, array $context = []): void
    {
        $templatedEmail = (new TemplatedEmail())
            ->from(new Address( $this->parameterBag->get('mailer_from_address'), $this->parameterBag->get('mailer_from_name')))
            ->to($to)
            ->subject($subject)
            ->htmlTemplate($templatePath)
            ->context($context);

        $this->mailer->send($templatedEmail);
    }

    public function sendTemplateContactEmail(string $from, string $subject, string $templatePath, array $context = [])
    {
        $response=[
            'status' => 'success',
            'message' => $this->translator->trans('common.flash.success.contact'),
        ];
        $templatedEmail = (new TemplatedEmail())
            ->from($from)
            ->to(new Address( $this->parameterBag->get('mailer_from_address'), $this->parameterBag->get('mailer_from_name')))
            ->subject("Nouveau message de contact : " . $subject)
            ->htmlTemplate($templatePath)
            ->context($context);
        try{
            $this->mailer->send($templatedEmail);
        } catch (TransportExceptionInterface $e) {
            $response = [
                'status' => 'error',
                'message' => $this->translator->trans('common.flash.error.contact'),
            ];
        }
        return $response;
    }
}