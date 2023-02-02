<?php

namespace App\Repository\Tontines;

use App\Entity\Tontines\Tontinetype;
use App\Repository\Methods\RepositoryGeneralTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Tontinetype|null find($id, $lockMode = null, $lockVersion = null)
 * @method Tontinetype|null findOneBy(array $criteria, array $orderBy = null)
 * @method Tontinetype[]    findAll()
 * @method Tontinetype[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TontinetypeRepository extends ServiceEntityRepository
{
    protected $alais = 'tty';

    use RepositoryGeneralTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tontinetype::class);
    }

    // /**
    //  * @return Tontinetype[] Returns an array of Tontinetype objects
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
    public function findOneBySomeField($value): ?Tontinetype
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
