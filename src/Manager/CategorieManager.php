<?php

namespace App\Manager;

use App\Entity\Categorie;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

class CategorieManager
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly Security $security,
    ){}

    public function saveCategorie(Categorie $categorie): void
    {
        /** @var User $me */
        $me = $this->security->getUser();

        $categorie->setCreatedBy($me);
        
        // Si aucune couleur n'est définie, assigner une couleur aléatoire
        if (!$categorie->getColor()) {
            $categorie->setColor($this->_color());
        }

        $this->entityManager->persist($categorie);
        $this->entityManager->flush();
    }
    
    private function _color(): string
    {
        $colors = [
            'red',
            'orange',
            'amber',
            'yellow',
            'lime',
            'green',
            'emerald',
            'teal',
            'cyan',
            'sky',
            'blue',
            'indigo',
            'violet',
            'purple',
            'fuchsia',
            'pink',
            'rose'
        ];
        
        return $colors[array_rand($colors)];
    }
}