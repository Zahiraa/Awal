<?php

namespace App\Repository;

use App\Entity\ContenuDiscussion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ContenuDiscussion>
 */
class ContenuDiscussionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ContenuDiscussion::class);
    }

    public function findAllQuery($search): QueryBuilder
    {
        $queryBuilder = $this->createQueryBuilder('a')
            ->orderBy('a.createdAt', 'DESC');

        if (!empty($search)) {
            $queryBuilder->andWhere('a.title LIKE :search OR a.content LIKE :search')
                ->setParameter('search', '%' . $search . '%');
        }

        return $queryBuilder;
    }

    public function findLatestPublishedByContenu(\App\Entity\Contenu $contenu): ?ContenuDiscussion
    {
        return $this->createQueryBuilder('c')
            ->where('c.statut = :status')
            ->andWhere('c.contenu = :contenu')
            ->setParameter('status', ContenuDiscussion::STATUT_PUBLISHED)
            ->setParameter('contenu', $contenu)
            ->orderBy('c.createdAt', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findAllArchive(): array
    {
        $now = new \DateTime();
        $startOfMonth = (clone $now)->modify('first day of this month')->setTime(0, 0, 0);

        return $this->createQueryBuilder('c')
            ->andWhere('c.statut = :status')
            ->andWhere('c.createdAt < :startOfMonth')
            ->setParameter('status', ContenuDiscussion::STATUT_PUBLISHED)
            ->setParameter('startOfMonth', $startOfMonth)
            ->orderBy('c.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
