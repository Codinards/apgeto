<?php

namespace App\Repository\Tontines;

use App\Entity\Tontines\Tontineur;
use App\Repository\Methods\RepositoryGeneralTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Tontineur|null find($id, $lockMode = null, $lockVersion = null)
 * @method Tontineur|null findOneBy(array $criteria, array $orderBy = null)
 * @method Tontineur[]    findAll()
 * @method Tontineur[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TontineurRepository extends ServiceEntityRepository
{
    protected $alias = 'tor';

    use RepositoryGeneralTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tontineur::class);
    }

    /**
     * @return Tontineur[] Returns an array of Tontineur objects
     *
     */
    public function findByUserId(int $userId)
    {
        return $this->createQueryBuilder('to')
            ->andWhere('to.user_id = :val')
            ->setParameter('val', $userId)
            ->orderBy('to.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /*
    public function findOneBySomeField($value): ?Tontineur
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
