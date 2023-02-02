<?php

namespace App\Repository\Tontines;

use App\Entity\Tontines\CotisationDay;
use App\Repository\Methods\RepositoryGeneralTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CotisationDay|null find($id, $lockMode = null, $lockVersion = null)
 * @method CotisationDay|null findOneBy(array $criteria, array $orderBy = null)
 * @method CotisationDay[]    findAll()
 * @method CotisationDay[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CotisationDayRepository extends ServiceEntityRepository
{
    use RepositoryGeneralTrait;

    private $alias = 'cd';

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CotisationDay::class);
    }

    // /**
    //  * @return CotisationDay[] Returns an array of CotisationDay objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?CotisationDay
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
