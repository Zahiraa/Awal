<?php

namespace App\Manager;

use App\Entity\Categorie;
use App\Entity\Cgu;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

class CguManager
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly Security $security,
    ){}

    public function saveCgu(Cgu $cgu): void
    {
        /** @var User $me */
        $me = $this->security->getUser();
        $cgu->setCreatedAt(new \DateTime());
        $cgu->setCreatedBy($me);
        $this->entityManager->persist($cgu);
        $this->entityManager->flush();
    }

    public function editCgu(Cgu $cgu): void
    {
        $cgu->setUpdatedAt(new \DateTime());
        $this->entityManager->flush();
    }

}