<?php

namespace App\Repository\Main\Assistances;

use App\Entity\Main\Assistances\AssistanceType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method AssistanceType|null find($id, $lockMode = null, $lockVersion = null)
 * @method AssistanceType|null findOneBy(array $criteria, array $orderBy = null)
 * @method AssistanceType[]    findAll()
 * @method AssistanceType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AssistanceTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AssistanceType::class);
    }

    // /**
    //  * @return AssistanceType[] Returns an array of AssistanceType objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?AssistanceType
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
