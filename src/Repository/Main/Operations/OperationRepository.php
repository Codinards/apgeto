<?php

namespace App\Repository\Main\Operations;

use App\Entity\Main\Operations\Operation;
use App\Entity\Main\Operations\Type;
use App\Repository\Main\Funds\AccountTraitRepository;
use App\Repository\Methods\RepositoryGeneralTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Operation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Operation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Operation[]    findAll()
 * @method Operation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OperationRepository extends ServiceEntityRepository
{
    use RepositoryGeneralTrait, AccountTraitRepository;
    private string $alias = 'ope';

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Operation::class);
    }


    public function sum(?Type $type = null, null|int|string $year = null): array
    {
        $builder = $this->createQueryBuilder($this->alias)
            ->select(
                "SUM(" . $this->alias . '.inflows) as inflows',
                "SUM(" . $this->alias . '.outflows) as outflows'
            );
        if ($type) {
            $builder->orWhere($this->alias . '.type = :type')
                ->setParameter('type', $type);
        } else {
            $builder->groupBy($this->alias . '.type');
        }
        if ($year) {
            $builder->orWhere($this->alias . '.year = :year')
                ->setParameter('year', $year);
        }
        return $builder->getQuery()->getResult();
    }


    public function findOperationsTypes(int|string $year): array
    {
        return $this->createQueryBuilder($this->alias)
            ->where($this->alias . '.year = :year')
            ->setParameter('year', $year)
            ->groupBy($this->alias . '.type')
            ->getQuery()
            ->getResult();
    }

    // /**
    //  * @return Operation[] Returns an array of Operation objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('o.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Operation
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
