<?php

namespace App\Repository\Tontines;

use App\Entity\Tontines\Tontine;
use App\Repository\Methods\RepositoryGeneralTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Tontine|null find($id, $lockMode = null, $lockVersion = null)
 * @method Tontine|null findOneBy(array $criteria, array $orderBy = null)
 * @method Tontine[]    findAll()
 * @method Tontine[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TontineRepository extends ServiceEntityRepository
{
    protected $alais = 'ton';

    use RepositoryGeneralTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tontine::class);
    }

    // /**
    //  * @return Tontine[] Returns an array of Tontine objects
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
    public function findOneBySomeField($value): ?Tontine
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
