<?php

namespace App\Repository\Main\Users;

use App\Entity\Main\Users\UserAction;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UserAction|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserAction|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserAction[]    findAll()
 * @method UserAction[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserActionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserAction::class);
    }

    public function findIsUpdatable(): array
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.isUpdatable = :val')
            ->setParameter('val', true)
            ->orderBy('u.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    // /**
    //  * @return UserAction[] Returns an array of UserAction objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?UserAction
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
