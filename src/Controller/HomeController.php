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
    public function home(ArticleRepository $articleRepository, ContenuRepository $contenuRepository, ContenuDiscussionRepository $contenuDiscussionRepository, TexteRepository $texteRepository, OpinionRepository $opinionRepository, AuthorRepository $authorRepository): Response
    {
        $contenu = $contenuRepository->findContentCurrentOrPreviousMonth();
        
        $contenuDiscussion = [];
        if($contenu){
            $contenuDiscussion = $contenuDiscussionRepository->findLatestPublishedByContenu($contenu);
        }
        $textesList = $texteRepository->findTexteCurrentOrPreviousMonth();
        $textesOnly = array_filter($textesList, fn($t) => $t->getType() === 'texte');
        $audiosOnly = array_filter($textesList, fn($t) => $t->getType() === 'audio');
        $opinion = $opinionRepository->findLastOpinion();
        $authors= $authorRepository->findAll();

        return $this->render('home/index.html.twig', [
            'contenu' => $contenu,
            'contenuDiscussion' => $contenuDiscussion,
            'textes' => $textesOnly,
            'audios' => $audiosOnly,
            'opinion' => $opinion,
            'authors' => $authors,
        ]);
    }

    #[Route(path: '/archive', name: 'archive', methods: ['GET'])]
    public function archive(): Response
    {
        return $this->render('archive/index.html.twig');
    }

    #[Route(path: '/archive/numeros', name: 'archive_numeros', methods: ['GET'])]
    public function archiveNumeros(ContenuRepository $contenuRepository): Response
    {
        $contenuArchive = $contenuRepository->findAllArchive();

        return $this->render('archive/numeros.html.twig', [
            'contenuArchive' => $contenuArchive,
        ]);
    }

    #[Route(path: '/archive/discussions', name: 'archive_discussions', methods: ['GET'])]
    public function archiveDiscussions(ContenuDiscussionRepository $contenuDiscussionRepository): Response
    {
        $discussionArchive = $contenuDiscussionRepository->findAllArchive();

        return $this->render('archive/discussions.html.twig', [
            'discussionArchive' => $discussionArchive,
        ]);
    }

    #[Route(path: '/archive/textes', name: 'archive_textes', methods: ['GET'])]
    public function archiveTextes(TexteRepository $texteRepository): Response
    {
        $textesArchive = $texteRepository->findAllArchive();

        // Separate texts and audios if needed on the twig side, but let's pass them all.
        // We will pass the whole result.
        return $this->render('archive/textes.html.twig', [
            'textesArchive' => $textesArchive,
        ]);
    }

    #[Route(path: '/contact', name: 'contact', methods: ['GET', 'POST'])]
    public function contact(Request $request, Mailler $mailler): Response
    {
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

            return $this->redirectToRoute('contact');
        }

        return $this->render('contact/index.html.twig', [
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


}
