<?php

namespace App\Controller;

use App\DTO\ContactDTO;
use App\Form\ContactForm;
use App\Manager\Mailler;
use App\Repository\ArticleRepository;
use App\Repository\AboutRepository;
use App\Repository\ContenuRepository;
use App\Repository\ContenuDiscussionRepository;
use App\Repository\TexteRepository;
use App\Repository\OpinionRepository;
use App\Repository\AuthorRepository;
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
    public function home(ArticleRepository $articleRepository, ContenuRepository $contenuRepository, ContenuDiscussionRepository $contenuDiscussionRepository, TexteRepository $texteRepository, OpinionRepository $opinionRepository, AboutRepository $aboutRepository, AuthorRepository $authorRepository,Mailler $mailler,Request $request): Response
    {
        $contenu = $contenuRepository->findContentCurrentOrPreviousMonth();
        // archive contenu all published contenu different than current month
        $contenuArchive = [];
        $contenuDiscussion = [];
        if($contenu){
            $contenuArchive = $contenuRepository->findContentArchive($contenu);
            $contenuDiscussion = $contenuDiscussionRepository->findLatestPublishedByContenu($contenu);
        }
        $textesList = $texteRepository->findTexteCurrentOrPreviousMonth();
        $textesOnly = array_filter($textesList, fn($t) => $t->getType() === 'texte');
        $audiosOnly = array_filter($textesList, fn($t) => $t->getType() === 'audio');
        $opinion = $opinionRepository->findLastOpinion();
        $about = $aboutRepository->findLastAbout();
        $authors= $authorRepository->findAll();

        $contact = new ContactDTO();
        $form = $this->createForm(ContactForm::class, $contact);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $context = ['admin' => $this->getParameter('mailer_from_name'), 'contact' => $contact];
            $response= $mailler->sendTemplateContactEmail($contact->getEmail(), $contact->getSubject(), 'emails/contact.html.twig',$context );
            if($response['status'] === 'success') {
                $this->addSuccessMessage($response['message']);
            } else {
                $this->addErrorMessage($response['message']);
            }

            return $this->redirectToRoute('home');
        }

        return $this->render('home/index.html.twig', [
            'contenu' => $contenu,
            'contenuArchive' => $contenuArchive,
            'contenuDiscussion' => $contenuDiscussion,
            'textes' => $textesOnly,
            'audios' => $audiosOnly,
            'opinion' => $opinion,
            'about' => $about,
            'authors' => $authors,
            'form' => $form,
        ]);
    }

    #[Route(path: '/about', name: 'about', methods: ['GET'])]
    public function about(AboutRepository $aboutRepository): Response
    {
        $about = $aboutRepository->findOneBy([], ['id' => 'DESC']);
        if(!$about) {
            $this->addInfoMessage('La page about n\'est pas encore disponible.');
            return $this->redirectToRoute('home');
        }
        return $this->render('about/index.html.twig', [
            'about' => $about,
        ]);
    }
 #[Route(path: '/whoAre', name: 'whoAre', methods: ['GET'])]
    public function whoAre(AboutRepository $aboutRepository): Response
    {
        $about = $aboutRepository->findOneBy([], ['created_at' => 'DESC']);
        if(!$about) {
            $this->addInfoMessage('La page about n\'est pas encore disponible.');
            return $this->redirectToRoute('home');
        }
        return $this->render('about/index.html.twig', [
            'about' => $about,
        ]);
    }

}
