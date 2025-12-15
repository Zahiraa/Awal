<?php

namespace App\Repository;

use App\Entity\Terme;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Terme>
 */
class TermeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Terme::class);
    }

    //    /**
    //     * @return terme[] Returns an array of terme objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('g')
    //            ->andWhere('g.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('g.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?terme
    //    {
    //        return $this->createQueryBuilder('g')
    //            ->andWhere('g.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }



    public function findAllQuery($search)
    {
        return $this->createQueryBuilder('t')
            ->leftJoin('t.contents', 'c')
            ->orderBy('t.createdAt', 'DESC')
            ->andWhere('c.titreDr LIKE :search  OR c.titreFr LIKE :search OR c.titreAr LIKE :search OR c.descriptionFr LIKE :search OR c.descriptionAr LIKE :search OR c.descriptionDr LIKE :search')
            ->setParameter('search', '%' . $search . '%');
    }


    public function findAllPublished($search)
    {
        return $this->createQueryBuilder('t')
            ->leftJoin('t.contents', 'c')
            ->orderBy('t.createdAt', 'DESC')
            ->andWhere('t.statut = :statut')
            ->andWhere('c.titreFr LIKE :search OR c.titreAr LIKE :search OR c.titreDr LIKE :search')
            ->setParameter('search', '%' . $search . '%')
            ->setParameter('statut', Terme::STATUT_PUBLISHED)
            ->getQuery()
            ->getResult();
    }

    public function findByStartWithLetter($letter)
    {
        return $this->createQueryBuilder('t')
            ->leftJoin('t.contents', 'c')
            ->andWhere('UPPER(c.titreFr) LIKE :letter')
            ->setParameter('letter', strtoupper($letter) . '%')
            ->andWhere('t.statut = :statut')
            ->setParameter('statut', Terme::STATUT_PUBLISHED)
            ->orderBy('c.titreFr', 'ASC')
            ->getQuery()
            ->getResult()
          ;
    }
}
