<?php

namespace App\Repository\Methods;

use Doctrine\ORM\QueryBuilder;

trait RepositoryGeneralTrait
{

    /**
     * @example findByConditions(["id" => $user, 'select' => 'name|pseudo', 'memberOf' => $tag, 'groupBy' => 'name']) description
     * @param array $criteria
     * @param array|null $orderBy
     * @param [type] $limit
     * @param [type] $offset
     * @return array
     */
    public function findByConditions(array $criteria, ?array $orderBy = null, $limit = null, $offset = null): array
    {
        $memberOf = [];
        if (isset($criteria['memberOf'])) {
            $memberOf = $criteria['memberOf'];
            unset($criteria['memberOf']);
        }
        if ($select = ($criteria['select'] ?? null)) {
            unset($criteria['select']);
        }

        if ($groupBy = ($criteria['groupBy'] ?? null)) {
            unset($criteria['groupBy']);
        }

        if ($in = ($criteria['in'] ?? null)) unset($criteria['in']);

        if ($notin = ($criteria['notin'] ?? null)) unset($criteria['notin']);

        /** \Doctrine\ORM\QueryBuilder $builder */
        $builder = $this->createQueryBuilder($this->alias);
        foreach ($criteria as $key => $value) {
            $operator = '=';
            if (is_array($value)) {
                $operator = trim($value[1]);
                $value = $value[0];
            }
            $testValue = is_string($value) ? strtolower($value) : null;
            if (!in_array($testValue, ['is null', 'is not null'])) {
                $builder->andWhere($this->alias . '.' . $key . " $operator :$key")
                    ->setParameter($key, $value);
            } else {
                $builder->andWhere($this->alias . '.' . $key . " $value");
            }
        }

        if ($select) {
            $select = array_map(fn ($elt) => $this->alias . '.' . $elt, is_array($select) ? $select : explode('|', $select));
            call_user_func_array([$builder, 'select'], $select);
        }
        if ($in) {
            $builder->andWhere($this->alias . '.' . $in['field'] . ' IN (' . join(', ', $in['values']) . ')');
        }

        if ($notin) {
            $builder->andWhere($this->alias . '.' . $notin['field'] . ' NOT IN (' . join(', ', $notin['values']) . ')');
        }

        if ($groupBy) {
            $builder->groupBy($this->alias . '.' . $groupBy);
        }

        foreach ($memberOf as $key => $value) {
            if (is_array($value)) {
                $sql = '';
                foreach ($value as $k => $val) {
                    $cle = 'element' . $k;
                    $builder->setParameter($cle, $val);
                    $sql .= ':' . $cle . ' member of ' . $this->alias . '.' . $key . ' OR ';
                }
                $sql .= substr($sql, 0, -3);
                $builder->andWhere($sql);
            } else {
                $builder->andWhere(':' . $key . ' member of ' . $this->alias . '.' . $key)
                    ->setParameter($key, $value);
            }
        }

        $orderBy = $orderBy ?? [];
        foreach ($orderBy as $key => $value) {
            $builder->orderBy($this->alias . '.' . $key, $value);
        }

        if ($limit) {
            $builder->setMaxResults($limit);
        }

        return $builder->getQuery()->getResult();
    }

    public function findByOr(array $fields, $value)
    {
        $builder = $this->createQueryBuilder($this->alias);
        foreach ($fields as $field) {
            $builder->orWhere($this->alias . '.' . $field . ' = :' . $field)
                ->setParameter($field, $value);
        }
        return $builder->getQuery()->getResult();
    }

    public function findOneByOr(array $fields, $value)
    {
        return $this->findByOr($fields, $value)[0] ?? null;
    }
}
