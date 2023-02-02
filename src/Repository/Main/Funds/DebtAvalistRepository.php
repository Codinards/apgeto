<?php

namespace App\Repository\Main\Funds;

use App\Entity\Main\Funds\DebtAvalist;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method DebtAvalist|null find($id, $lockMode = null, $lockVersion = null)
 * @method DebtAvalist|null findOneBy(array $criteria, array $orderBy = null)
 * @method DebtAvalist[]    findAll()
 * @method DebtAvalist[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DebtAvalistRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DebtAvalist::class);
    }

    // /**
    //  * @return DebtAvalist[] Returns an array of DebtAvalist objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?DebtAvalist
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
