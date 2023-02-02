<?php

namespace App\Repository\Interests;

use App\Entity\Interests\UserInterest;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UserInterest|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserInterest|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserInterest[]    findAll()
 * @method UserInterest[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserInterestRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserInterest::class);
    }

    // /**
    //  * @return UserInterest[] Returns an array of UserInterest objects
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
    public function findOneBySomeField($value): ?UserInterest
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
