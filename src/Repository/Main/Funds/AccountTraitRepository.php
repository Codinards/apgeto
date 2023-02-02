<?php

namespace App\Repository\Main\Funds;

use App\Entity\Main\Funds\Debt;
use App\Entity\Main\Funds\Fund;
use App\Repository\Methods\RepositoryGeneralTrait;

trait AccountTraitRepository
{
    use RepositoryGeneralTrait;

    public function findYears($grouped = true)
    {
        $builder = $this->createQueryBuilder($this->alias)
            ->select($this->alias . '.year');
        if ($grouped) {
            $builder->groupBy($this->alias . '.year');
        }
        return $builder->getQuery()->getResult();
    }

    public function findLast(array $conditions, $field = 'createdAt', $order = 'DESC')
    {
        return $this->findByConditions($conditions, [$field => $order])[0] ?? null;
        $builder = $this->createQueryBuilder($this->alias);
        foreach ($conditions as $key => $value) {
            $builder->andWhere($this->alias . '.' . $key . " = :$key")
                ->setParameter($key, $value);
        }
        return $builder
            ->orderBy($this->alias . '.' . $field, $order)
            ->getQuery()->getResult()[0] ?? null;
    }

    /**
     * select all entity created after a given conditions
     * 
     * @param \DateTimeInterface $date
     * @param string $operator
     * @param string $field
     * @param array $conditions
     * @return object[]|object
     */
    public function findWhereCreatedAt(
        \DateTimeInterface $date,
        string $operator = ">=",
        string $field = "createdAt",
        array $conditions = [],
        bool $getOneResult = false,
        bool $orderDesc = true
    ): null|object|array {
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

    /**
     * @param int|string $year
     * @param null|string $groupBy
     * @return object[] Returns an array of Fund objects
     */
    public function findByYear(int|string $year, ?string $orderBy = null): array
    {
        $builder = $this->createQueryBuilder($this->alias)
            ->andWhere($this->alias . '.year = :year')
            ->setParameter('year', $year);
        if ($orderBy !== null && property_exists($this->_entityName, $orderBy)) {
            $builder->orderBy($this->alias . '.' . $orderBy);
        }
        return $builder->getQuery()
            ->getResult();
    }


    public function findOrderByUser(int|string $year)
    {
        return $this->findByYear($year, 'user');
    }
}
