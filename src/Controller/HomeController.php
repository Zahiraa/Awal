<?php

namespace App\Controller;

use App\DTO\ContactDTO;
use App\Entity\Terme;
use App\Form\ContactForm;
use App\Manager\Mailler;
use App\Repository\ArticleRepository;
use App\Repository\CguRepository;
use App\Repository\TermeRepository;
use Karser\Recaptcha3Bundle\Validator\Constraints\Recaptcha3Validator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Contracts\Translation\TranslatorInterface;

class HomeController extends DefaultController
{
    #[Route('/change-locale/{locale}', name: 'change_locale')]
    public function changeLocale(Request $request, string $locale): RedirectResponse
    {
        $request->getSession()->set('_locale', $locale);

        $referer = $request->headers->get('referer');
        return $this->redirect($referer ?: $this->generateUrl('home', ['_locale' => $locale]));
    }

    #[Route(path: '/', name: 'home')]
    public function home(ArticleRepository $articleRepository): Response
    {
        $articles= $articleRepository->findPublishedArticles(3);
        return $this->render('home/index.html.twig', [
            'articles' => $articles,
        ]);
    }

    #[Route(path: 'conditions-generales', name: 'conditions_generales', methods: ['GET'])]
    public function conditionsGenerales(CguRepository $cguRepository): Response
    {
        $cgu = $cguRepository->findOneBy([], ['created_at' => 'DESC']);
        if(!$cgu) {
            $this->addInfoMessage('La page des conditions générales n\'est pas encore disponible.');
            return $this->redirectToRoute('home');
        }
        return $this->render('cgu/conditions-generales.html.twig', [
            'cgu' => $cgu,
        ]);
    }

    #[Route(path: 'contact', name: 'contact', methods: ['GET', 'POST'])]
    public function contact(Request $request, Mailler $mailler, Recaptcha3Validator $recaptcha3Validator, TranslatorInterface $translator): Response
    {
        $contact = new ContactDTO();
        $form = $this->createForm(ContactForm::class, $contact);
        $form->handleRequest($request);
        $score=0;
        if ($form->isSubmitted() && $form->isValid()) {
            if($recaptcha3Validator->getLastResponse()) {
                $score = $recaptcha3Validator->getLastResponse()->getScore();
            }

            $recaptcha=$form->get('recaptcha')->getData();
            if ($score < 0.5 || !$recaptcha) {
                $this->addErrorMessage($translator->trans('ContactSection.page.form.recaptcha.error'));
                return $this->redirectToRoute('contact');
            }

            $context = ['admin' => $this->getParameter('mailer_from_name'), 'contact' => $contact];
            $response= $mailler->sendTemplateContactEmail($contact->getEmail(), $contact->getSubject(), 'emails/contact.html.twig',$context );
            if($response['status'] === 'success') {
                $this->addSuccessMessage($response['message']);
            } else {
                $this->addErrorMessage($response['message']);
            }

            return $this->redirectToRoute('contact');
        }

        return $this->render('contact/index.html.twig', [
            'form' => $form
        ]);
    }
}
