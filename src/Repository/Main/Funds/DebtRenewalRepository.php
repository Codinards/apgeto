<?php

namespace App\Repository\Main\Funds;

use App\Entity\Main\Funds\DebtRenewal;
use App\Repository\Methods\RepositoryGeneralTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method DebtRenewal|null find($id, $lockMode = null, $lockVersion = null)
 * @method DebtRenewal|null findOneBy(array $criteria, array $orderBy = null)
 * @method DebtRenewal[]    findAll()
 * @method DebtRenewal[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DebtRenewalRepository extends ServiceEntityRepository
{
    use RepositoryGeneralTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DebtRenewal::class);
    }

    // /**
    //  * @return DebtRenewal[] Returns an array of DebtRenewal objects
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
    public function findOneBySomeField($value): ?DebtRenewal
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
