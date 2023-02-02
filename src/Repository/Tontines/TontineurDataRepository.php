<?php

namespace App\Repository\Tontines;

use App\Entity\Tontines\TontineurData;
use App\Repository\Methods\RepositoryGeneralTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TontineurData|null find($id, $lockMode = null, $lockVersion = null)
 * @method TontineurData|null findOneBy(array $criteria, array $orderBy = null)
 * @method TontineurData[]    findAll()
 * @method TontineurData[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TontineurDataRepository extends ServiceEntityRepository
{
    protected $alias = 'td';

    use RepositoryGeneralTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TontineurData::class);
    }

    // /**
    //  * @return TontineurData[] Returns an array of TontineurData objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?TontineurData
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
