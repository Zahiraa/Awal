<?php

namespace App\Controller\admin;

use App\Controller\DefaultController;
use App\Entity\Cgu;
use App\Form\CguForm;
use App\Manager\CguManager;
use App\Repository\CguRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/cgu')]
final class AdminCguController extends DefaultController
{
    #[Route(name: 'app_admin_cgu_index', methods: ['GET'])]
    public function index(CguRepository $cguRepository): Response
    {

        return $this->render('admin/admin_cgu/index.html.twig', [
            'documents' => $cguRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_admin_cgu_new', methods: ['GET', 'POST'])]
    public function new(Request $request,CguManager $manager,EntityManagerInterface $entityManager): Response
    {
        // check if cgu already exists
        $existingCgu = $entityManager->getRepository(Cgu::class)->findAll();
        if ($existingCgu) {
            $this->addWarningMessage('Les CGU existent déjà. Vous ne pouvez pas en créer de nouvelles.');
            return $this->redirectToRoute('app_admin_cgu_index');
        }
        $cgu = new Cgu();
        $form = $this->createForm(CguForm::class, $cgu);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $manager->saveCgu($cgu);
            $this->addSuccessMessage('Les CGU ont bien été créées.');
            return $this->redirectToRoute('app_admin_cgu_index');
        }
        return $this->render('admin/admin_cgu/new.html.twig', [
            'cgu' => $cgu,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_cgu_show', methods: ['GET'])]
    public function show(int $id,CguRepository $cguRepository): Response
    {
        $cgu = $cguRepository->find($id);
        if(!$cgu) {
            $this->addErrorMessage('Le document n\'existe pas.');
            return $this->redirectToRoute('app_admin_cgu_index');
        }
        return $this->render('admin/admin_cgu/show.html.twig', [
            'cgu' => $cgu,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_cgu_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request,int $id,CguRepository $cguRepository,CguManager $manager): Response
    {
        $cgu = $cguRepository->find($id);
        if(!$cgu) {
            $this->addErrorMessage('Le document n\'existe pas.');
            return $this->redirectToRoute('app_admin_cgu_index');
        }
        $form = $this->createForm(CguForm::class, $cgu);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $manager->editCgu($cgu);
            $this->addSuccessMessage('Les CGU ont bien été modifiées.');
            return $this->redirectToRoute('app_admin_cgu_index');
        }
        return $this->render('admin/admin_cgu/edit.html.twig', [
            'cgu' => $cgu,
            'form' => $form,
        ]);
    }
    #[Route('/{id}', name: 'app_admin_cgu_delete', methods: ['POST'])]
    public function delete(Request $request, Cgu $cgu, EntityManagerInterface $entityManager ): Response
    {
        if ($this->isCsrfTokenValid('delete' . $cgu->getId(), $request->request->get('_token'))) {
            $entityManager->remove($cgu);
            $entityManager->flush();
            $this->addSuccessMessage('Le document a bien été supprimé.');
        } else {
            $this->addErrorMessage('Une erreur est survenue lors de la suppression du document.');
        }
        return $this->redirectToRoute('app_admin_cgu_index');
    }
}
