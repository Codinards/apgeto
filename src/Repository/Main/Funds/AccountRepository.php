<?php

namespace App\Repository\Main\Funds;

use App\Entity\Main\Funds\Account;
use App\Entity\Main\Users\User;
use App\Entity\Utils\ContributorFilter;
use App\Repository\Methods\RepositoryGeneralTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Account|null find($id, $lockMode = null, $lockVersion = null)
 * @method Account|null findOneBy(array $criteria, array $orderBy = null)
 * @method Account[]    findAll()
 * @method Account[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AccountRepository extends ServiceEntityRepository
{
    use RepositoryGeneralTrait;
    private $alias = 'ac';
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Account::class);
    }

    /**
     * retrieve users that respect some fliter conditions
     *
     * @param ContributorFilter $filter
     * @return array
     * @psalm-return <T> the entities
     */
    public function findWithFilter(ContributorFilter $filter): array
    {
        $builder = $this->createQueryBuilder($this->alias);
        if ($filter->getMaxCashBalance()) {
            $builder->andWhere($this->alias . '.cashBalances <= :max')
                ->setParameter('max', $filter->getMaxCashBalance());
        }
        if ($filter->getMinCashBalance()) {
            $builder->andWhere($this->alias . '.cashBalances >= :min')
                ->setParameter('min', $filter->getMinCashBalance());
        }
        return $builder->getQuery()->getResult();
    }


    public function findWhere($field, $operator, $value): array
    {
        $operators = [
            'eq' => '=', 'inf' => '<', 'sup' => '>', 'infeq' => '<=', 'supeq' => '>='
        ];
        $fields = ['fund' => 'cashBalances', 'loan' => 'loanBalances'];
        return $this->createQueryBuilder($this->alias)
            ->where($this->alias . '.' . $fields[$field] . ' ' . $operators[$operator] . ' :' . $field)
            ->setParameter($field, $value)
            ->getQuery()
            ->getResult();
    }

    public function findNotLocked(): array
    {
        return $this->createQueryBuilder($this->alias)
            ->where($this->alias . '.user IN (SELECT u FROM ' . User::class . ' as u WHERE u.locked <> TRUE)')
            ->getQuery()
            ->getResult();
    }

    public function findLocked(): array
    {
        return $this->createQueryBuilder($this->alias)
            ->where($this->alias . '.user IN (SELECT u FROM ' . User::class . ' as u WHERE u.locked = TRUE)')
            ->getQuery()
            ->getResult();
    }

    // /**
    //  * @return Account[] Returns an array of Account objects
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
    public function findOneBySomeField($value): ?Account
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
