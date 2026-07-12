<?php

namespace App\Repository;

use App\Entity\Author;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Author>
 */
class AuthorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Author::class);
    }

    //    /**
    //     * @return Author[] Returns an array of Author objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('a.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Author
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
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
            $queryBuilder->andWhere('a.name LIKE :search OR a.nameFr LIKE :search OR a.country LIKE :search')
                ->setParameter('search', '%' . $search . '%');
        }

        return $queryBuilder;
    }

    public function findAuthorsWithPublishedArticlesInCurrentMonth(): array
    {
        $now = new \DateTime();
        $startOfMonth = (clone $now)->modify('first day of this month')->setTime(0, 0, 0);
        $endOfMonth = (clone $now)->modify('last day of this month')->setTime(23, 59, 59);

        return $this->createQueryBuilder('a')
            ->join('a.articles', 'art')
            ->where('art.statut = :status')
            ->andWhere('art.createdAt BETWEEN :start AND :end')
            ->setParameter('status', \App\Entity\Article::STATUT_PUBLISHED)
            ->setParameter('start', $startOfMonth)
            ->setParameter('end', $endOfMonth)
            ->groupBy('a.id')
            ->getQuery()
            ->getResult();
    }
}
