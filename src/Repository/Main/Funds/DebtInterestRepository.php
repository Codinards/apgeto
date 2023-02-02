<?php

namespace App\Repository\Main\Funds;

use App\Entity\Main\Funds\DebtInterest;
use App\Repository\Methods\RepositoryGeneralTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method DebtInterest|null find($id, $lockMode = null, $lockVersion = null)
 * @method DebtInterest|null findOneBy(array $criteria, array $orderBy = null)
 * @method DebtInterest[]    findAll()
 * @method DebtInterest[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DebtInterestRepository extends ServiceEntityRepository
{
    use RepositoryGeneralTrait;
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DebtInterest::class);
    }

    // /**
    //  * @return DebtInterest[] Returns an array of DebtInterest objects
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
    public function findOneBySomeField($value): ?DebtInterest
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
