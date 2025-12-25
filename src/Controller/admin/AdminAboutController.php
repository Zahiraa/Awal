<?php

namespace App\Controller\Admin;

use App\Controller\DefaultController;
use App\Entity\About;
use App\Form\AboutForm;
use App\Manager\AboutManager;
use App\Repository\AboutRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/about')]
final class AdminAboutController extends DefaultController
{
    #[Route(name: 'app_admin_about_index', methods: ['GET'])]
    public function index(AboutRepository $aboutRepository): Response
    {

        return $this->render('admin/admin_about/index.html.twig', [
            'documents' => $aboutRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_admin_about_new', methods: ['GET', 'POST'])]
    public function new(Request $request,AboutManager $manager,EntityManagerInterface $entityManager): Response
    {
        // check if about already exists
        $existingAbout = $entityManager->getRepository(About::class)->findAll();
        if ($existingAbout) {
            $this->addWarningMessage('La page "À propos" existe déjà. Vous ne pouvez pas en créer de nouvelles.');
            return $this->redirectToRoute('app_admin_about_index');
        }
        $about = new About();
        $form = $this->createForm(AboutForm::class, $about);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $image = $form->get('image')->getData();
            $manager->saveAbout($about, $image);
            $this->addSuccessMessage('La page "À propos" a bien été créée.');
            return $this->redirectToRoute('app_admin_about_index');
        }
        return $this->render('admin/admin_about/new.html.twig', [
            'about' => $about,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_about_show', methods: ['GET'])]
    public function show(int $id,AboutRepository $aboutRepository): Response
    {
        $about = $aboutRepository->find($id);
        if(!$about) {
            $this->addErrorMessage('Le document n\'existe pas.');
            return $this->redirectToRoute('app_admin_about_index');
        }
        return $this->render('admin/admin_about/show.html.twig', [
            'about' => $about,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_about_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request,int $id,AboutRepository $aboutRepository,AboutManager $manager): Response
    {
        $about = $aboutRepository->find($id);
        if(!$about) {
            $this->addErrorMessage('Le document n\'existe pas.');
            return $this->redirectToRoute('app_admin_about_index');
        }
        $form = $this->createForm(AboutForm::class, $about);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $image = $form->get('image')->getData();
            $manager->editAbout($about, $image);
            $this->addSuccessMessage('La page "À propos" a bien été modifiée.');
            return $this->redirectToRoute('app_admin_about_index');
        }
        return $this->render('admin/admin_about/edit.html.twig', [
            'about' => $about,
            'form' => $form,
            'image' => $about->getImage(),
        ]);
    }
    #[Route('/{id}', name: 'app_admin_about_delete', methods: ['POST'])]
    public function delete(Request $request, About $about, EntityManagerInterface $entityManager ): Response
    {
        if ($this->isCsrfTokenValid('delete' . $about->getId(), $request->request->get('_token'))) {
            $entityManager->remove($about);
            $entityManager->flush();
            $this->addSuccessMessage('Le document a bien été supprimé.');
        } else {
            $this->addErrorMessage('Une erreur est survenue lors de la suppression du document.');
        }
        return $this->redirectToRoute('app_admin_about_index');
    }
}
