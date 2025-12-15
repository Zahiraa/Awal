<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Gedmo\SoftDeleteable\SoftDeleteableListener;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    public function findUsersByRoles(array $roles)
    {
        $dql = 'SELECT u FROM App\Entity\User u WHERE ';
        $orConditions = [];
        $parameters = [];

        foreach ($roles as $key => $role) {
            $paramName = 'role' . $key;
            $orConditions[] = "u.roles LIKE :" . $paramName;
            $parameters[$paramName] = '%"' . $role . '"%';
        }

        $dql .= implode(' OR ', $orConditions);

        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameters($parameters);

        return $query->getResult();
    }

    /**
     * Find all users including deleted ones
     *
     * @return User[]
     */
    public function findAllWithDeleted(): array
    {
        $filters = $this->getEntityManager()->getFilters();
        $filters->disable('softdeleteable');
        
        $users = $this->findAll();
        
        $filters->enable('softdeleteable');
        
        return $users;
    }

    /**
     * Find one user by id including deleted ones
     */
    public function findOneWithDeleted(int $id): ?User
    {
        $filters = $this->getEntityManager()->getFilters();
        $filters->disable('softdeleteable');
        
        $user = $this->find($id);
        
        $filters->enable('softdeleteable');
        
        return $user;
    }

    // get users not deleted

    public function findAllUsers($search)
    {
        return $this->createQueryBuilder('u')
            ->orderBy('u.id', 'DESC')
            ->andWhere('u.deletedAt IS NULL')
            ->andWhere('LOWER(TRIM(u.email)) LIKE :search OR LOWER(TRIM(u.prenom)) LIKE :search OR LOWER(TRIM(u.nom)) LIKE :search OR LOWER(TRIM(u.statut)) LIKE :search OR LOWER(u.roles) LIKE :search')
            ->setParameter('search', '%' . strtolower(trim($search)) . '%');
    }

    public function invitedUser($email)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.email = :email')
            ->andWhere('(u.registrationToken IS NOT NULL OR u.invitedAt IS NOT NULL)')
            ->setParameter('email', $email)
            ->getQuery()
            ->getOneOrNullResult();
    }
    public function checkUser($email)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.email = :email')
            ->andWhere('(u.invitedAt IS NULL)')
            ->setParameter('email', $email)
            ->getQuery()
            ->getOneOrNullResult();
    }
    //    /**
    //     * @return User[] Returns an array of User objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('u.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?User
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
