<?php

namespace App\Repository\Main\Operations;

use App\Entity\Main\Operations\Operation;
use App\Entity\Main\Operations\Type;
use App\Repository\Methods\RepositoryGeneralTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Type|null find($id, $lockMode = null, $lockVersion = null)
 * @method Type|null findOneBy(array $criteria, array $orderBy = null)
 * @method Type[]    findAll()
 * @method Type[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TypeRepository extends ServiceEntityRepository
{
    use RepositoryGeneralTrait;

    private $alias = 'opt';

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Type::class);
    }

    public function findExcept(array $types)
    {
        $builder = $this->createQueryBuilder($this->alias);
        foreach ($types as $key => $type) {
            if ($type instanceof Type) {
                $type = $type;
            } elseif ($type instanceof Operation) {
                $type = $type->getType();
            }
            $builder->andWhere($this->alias . '.id <> :type' . $key)
                ->setParameter('type' . $key, $type);
        }
        return $builder->getQuery()->getResult();
    }

    // /**
    //  * @return Type[] Returns an array of Type objects
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
    public function findOneBySomeField($value): ?Type
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
