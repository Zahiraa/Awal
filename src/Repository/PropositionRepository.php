<?php

namespace App\Repository;

use App\Entity\Proposition;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Proposition>
 */
class PropositionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Proposition::class);
    }

    //    /**
    //     * @return proposition[] Returns an array of proposition objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?proposition
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }



    public function findQueryByUser($user,$search)
    {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.contents', 'c')
            ->where('p.createdBy = :user')
            ->setParameter('user', $user)
            ->orderBy('p.createdAt', 'DESC')
            ->andWhere('c.titreFr LIKE :search OR c.titreAr LIKE :search OR c.titreDr LIKE :search OR c.descriptionFr LIKE :search OR c.descriptionAr LIKE :search OR c.descriptionDr LIKE :search')
            ->setParameter('search', '%' . $search . '%');
    }

    public function findQueryAll($search)
    {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.contents', 'c')
            ->orderBy('p.createdAt', 'DESC')
            ->andWhere('c.titreFr LIKE :search OR c.titreAr LIKE :search OR c.titreDr LIKE :search OR c.descriptionFr LIKE :search OR c.descriptionAr LIKE :search OR c.descriptionDr LIKE :search')
            ->setParameter('search', '%' . $search . '%');
    }

}
