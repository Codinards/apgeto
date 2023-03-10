<?php

namespace App\Repository\Tontines;

use App\Entity\Tontines\Unity;
use App\Repository\Methods\RepositoryGeneralTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Unity|null find($id, $lockMode = null, $lockVersion = null)
 * @method Unity|null findOneBy(array $criteria, array $orderBy = null)
 * @method Unity[]    findAll()
 * @method Unity[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UnityRepository extends ServiceEntityRepository
{
    protected $alias = 'un';
    use RepositoryGeneralTrait;
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Unity::class);
    }

    // /**
    //  * @return Unity[] Returns an array of Unity objects
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
    public function findOneBySomeField($value): ?Unity
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
