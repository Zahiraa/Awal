<?php


namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{

    public function addSuccessMessage($message = "L'opération a été effectuée avec success !")
    {
        $this->addFlash('success', $message);
    }

    public function addErrorMessage($message = "Une erreur a été rencontrée !")
    {
        $this->addFlash('error', $message);
    }

    public function addInfoMessage($message)
    {
        $this->addFlash('info', $message);
    }

    public function addWarningMessage($message)
    {
        $this->addFlash('warning', $message);
    }


}