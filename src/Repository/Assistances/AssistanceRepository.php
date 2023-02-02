<?php

namespace App\Repository\Assistances;

use App\Entity\Assistances\Assistance;
use App\Entity\Assistances\Contributor;
use App\Entity\Utils\AssistanceFilter;
use App\Repository\Methods\RepositoryGeneralTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Assistance|null find($id, $lockMode = null, $lockVersion = null)
 * @method Assistance|null findOneBy(array $criteria, array $orderBy = null)
 * @method Assistance[]    findAll()
 * @method Assistance[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AssistanceRepository extends ServiceEntityRepository
{
    use RepositoryGeneralTrait;

    protected $alias = 'ass';

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Assistance::class);
    }

    public function findWithFilter(AssistanceFilter $filter, array $criteria = [])
    {
        if (!empty($filter)) {
            $filter = $filter->toArray();
            if ($contributor = ($filter['contributor'] ?? null)) {
                /** @var ContributorRepository $Contributorrepo */
                $Contributorrepo = $this->_em->getRepository(Contributor::class);
                $contributors = $Contributorrepo->findByConditions(['user' => $contributor->getUser()]);
                if (!empty($contributors)) $criteria['memberOf'] = ['contributors' => $contributors];
                unset($filter['contributor']);
            }
            $criteria = array_merge($criteria, $filter);
        }
        return $this->findByConditions($criteria);
    }

    public function findYears(): array
    {
        return $this->findByConditions(['select' => 'year', 'groupBy' => 'year']);
    }

    public function findByUserDistinct(array $conditions = [])
    {
        $conditions['groupBy'] = 'user';

        return $this->findByConditions($conditions);
    }
}
