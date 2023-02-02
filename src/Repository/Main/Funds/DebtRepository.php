<?php

namespace App\Repository\Main\Funds;

use App\Entity\Main\Funds\Debt;
use App\Repository\Methods\RepositoryGeneralTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Debt|null find($id, $lockMode = null, $lockVersion = null)
 * @method Debt|null findOneBy(array $criteria, array $orderBy = null)
 * @method Debt[]    findAll()
 * @method Debt[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DebtRepository extends ServiceEntityRepository
{
    use AccountTraitRepository; //, RepositoryGeneralTrait;

    private $alias = 'de';

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Debt::class);
    }

    public function findUsersBalance(int|string $year, ?string $groupBy = null)
    {
        $builder = $this->createQueryBuilder($this->alias)
            ->select(
                "{$this->alias}.previousBalances as previous",
                "SUM({$this->alias}.loanInFlows) as inflows",
                "SUM({$this->alias}.loanOutFlows) as outflows"
            )
            ->andWhere($this->alias . '.year = :year')
            ->setParameter('year', $year);
        if ($groupBy !== null && property_exists(Debt::class, $groupBy)) {
            $builder->groupBy($this->alias . '.' . $groupBy);
        }
        return $builder->getQuery()
            ->getResult();
    }

    // /**
    //  * @return Debt[] Returns an array of Debt objects
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
    public function findOneBySomeField($value): ?Debt
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
