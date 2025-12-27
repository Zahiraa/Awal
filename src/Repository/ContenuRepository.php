<?php

namespace App\Repository;

use App\Entity\Contenu;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Contenu>
 */
class ContenuRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Contenu::class);
    }

    //    /**
    //     * @return Contenu[] Returns an array of Contenu objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Contenu
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
    public function findContentCurrentOrPreviousMonth(): ?Contenu
    {
        $now = new \DateTime();
        $startOfMonth = (clone $now)->modify('first day of this month')->setTime(0, 0, 0);
        $endOfMonth = (clone $now)->modify('last day of this month')->setTime(23, 59, 59);

        $qb = $this->createQueryBuilder('c')
            ->where('c.statut = :status')
            ->andWhere('c.createdAt BETWEEN :start AND :end')
            ->setParameter('status', Contenu::STATUT_PUBLISHED)
            ->setParameter('start', $startOfMonth)
            ->setParameter('end', $endOfMonth)
            ->orderBy('c.createdAt', 'DESC')
            ->setMaxResults(1);
        
        $result = $qb->getQuery()->getOneOrNullResult();

        if ($result) {
            return $result;
        }

        // Try previous month
        $startOfPrevMonth = (clone $now)->modify('first day of last month')->setTime(0, 0, 0);
        $endOfPrevMonth = (clone $now)->modify('last day of last month')->setTime(23, 59, 59);

        $qb->setParameter('start', $startOfPrevMonth)
           ->setParameter('end', $endOfPrevMonth);
        
        return $qb->getQuery()->getOneOrNullResult();
    }
    public function findContentArchive(?Contenu $excludeContent = null): array
    {
        $qb = $this->createQueryBuilder('c')
            ->andWhere('c.statut = :status')
            ->setParameter('status', Contenu::STATUT_PUBLISHED)
            ->orderBy('c.createdAt', 'DESC');

        if ($excludeContent) {
            $excludeDate = $excludeContent->getCreatedAt();
            $startOfExcludedMonth = (clone $excludeDate)->modify('first day of this month')->setTime(0, 0, 0);

            $qb->andWhere('c.createdAt < :startOfExcludedMonth')
               ->setParameter('startOfExcludedMonth', $startOfExcludedMonth);
        }

        return $qb->getQuery()->getResult();
    }
}
