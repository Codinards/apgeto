<?php

namespace App\Repository\Main\Funds;


use App\Entity\Main\Funds\Fund;
use App\Repository\Methods\RepositoryGeneralTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use stdClass;

/**
 * @method Fund|null find($id, $lockMode = null, $lockVersion = null)
 * @method Fund|null findOneBy(array $criteria, array $orderBy = null)
 * @method Fund[]    findAll()
 * @method Fund[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FundRepository extends ServiceEntityRepository
{
    use AccountTraitRepository; //, RepositoryGeneralTrait;

    private  $alias = 'fu';

    private $saver;
    public function __construct(ManagerRegistry $registry)
    {
        $this->saver = new stdClass();
        parent::__construct($registry, Fund::class);
    }

    /**
     * @param int|string $year
     * @param null|string $groupBy
     * @return Fund[] Returns an array of Fund objects
     */
    public function findByYear(int|string $year, ?string $orderBy = null): array
    {
        $builder = $this->createQueryBuilder($this->alias)
            ->andWhere($this->alias . '.year = :year')
            ->setParameter('year', $year);
        if ($orderBy !== null && property_exists(Fund::class, $orderBy)) {
            $builder->orderBy($this->alias . '.' . $orderBy);
        }
        return $builder->getQuery()
            ->getResult();
    }

    public function findUsersBalance(int|string $year, ?string $groupBy = null)
    {
        $builder = $this->createQueryBuilder($this->alias)
            ->select(
                "{$this->alias}.previousBalances as previous",
                "SUM({$this->alias}.cashInFlows) as inflows",
                "SUM({$this->alias}.cashOutFlows) as outflows"
            )
            ->andWhere($this->alias . '.year = :year')
            ->setParameter('year', $year);
        if ($groupBy !== null && property_exists(Fund::class, $groupBy)) {
            $builder->groupBy($this->alias . '.' . $groupBy);
        }
        return $builder->getQuery()
            ->getResult();
    }

    public function findOrderByUser(int|string $year)
    {
        return $this->findByYear($year, 'user');
    }

    /**
     * select all fund created after a given conditions
     * 
     * @param \DateTimeInterface $date
     * @param string $operator
     * @param string $field
     * @param array $conditions
     * @return Fund[]
     */
    public function findWhereCreatedAt(
        \DateTimeInterface $date,
        string $operator = ">=",
        string $field = "createdAt",
        array $conditions = [],
        bool $getOneResult = false,
        bool $orderDesc = true
    ): null|Fund|array {
        $builder = $this->createQueryBuilder($this->alias)
            ->andWhere($this->alias . '.' . $field . " $operator :$field")
            ->setParameter($field, $date)
            // ->andWhere("{$this->alias}.$field > :base")
            // ->setParameter("base", new \DateTime("2021-12-01"))
        ;
        foreach ($conditions as $key => $value) {
            $operator = "=";
            if (is_array($value)) {
                $operator = $value[1];
                $value = $value[0];
            }
            $builder->andWhere("{$this->alias}.$key $operator :$key")
                ->setParameter($key, $value);
        }
        $builder->orderBy("{$this->alias}.$field", $orderDesc ? "DESC" : "ASC");

        if ($getOneResult) {
            return $builder
                ->setMaxResults(1)
                ->getQuery()
                ->getResult()[0] ?? null;
        }
        return $builder->getQuery()
            ->getResult();
    }
}
