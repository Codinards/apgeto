<?php

namespace App\Repository\Tontines;

use App\Entity\Tontines\CotisationFailure;
use App\Repository\Methods\RepositoryGeneralTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CotisationStatus|null find($id, $lockMode = null, $lockVersion = null)
 * @method CotisationStatus|null findOneBy(array $criteria, array $orderBy = null)
 * @method CotisationStatus[]    findAll()
 * @method CotisationStatus[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CotisationFailureRepository extends ServiceEntityRepository
{

    protected $alias = 'cf';

    use RepositoryGeneralTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CotisationFailure::class);
    }


    // /**
    //  * @return CotisationStatus[] Returns an array of CotisationStatus objects
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
    public function findOneBySomeField($value): ?CotisationStatus
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
