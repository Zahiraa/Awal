<?php

namespace App\Repository;

use App\Entity\Texte;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Texte>
 */
class TexteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Texte::class);
    }

    //    /**
    //     * @return Texte[] Returns an array of Texte objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('t.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Texte
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

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
    public function findTexteCurrentOrPreviousMonth()
    {
        $now = new \DateTime();
        $startOfMonth = (clone $now)->modify('first day of this month')->setTime(0, 0, 0);
        $endOfMonth = (clone $now)->modify('last day of this month')->setTime(23, 59, 59);

        $qb = $this->createQueryBuilder('t')
            ->where('t.statut = :status')
            ->andWhere('t.createdAt BETWEEN :start AND :end')
            ->setParameter('status', Texte::STATUT_PUBLISHED)
            ->setParameter('start', $startOfMonth)
            ->setParameter('end', $endOfMonth)
            ->orderBy('t.createdAt', 'DESC')
           ;
        
        $result = $qb->getQuery()->getResult();

        if ($result) {
            return $result;
        }

        // Try previous month
        $startOfPrevMonth = (clone $now)->modify('first day of last month')->setTime(0, 0, 0);
        $endOfPrevMonth = (clone $now)->modify('last day of last month')->setTime(23, 59, 59);

        $qb->setParameter('start', $startOfPrevMonth)
           ->setParameter('end', $endOfPrevMonth);

        return $qb->getQuery()->getResult();
    }
}
