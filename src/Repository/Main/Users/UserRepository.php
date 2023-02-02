<?php

namespace App\Repository\Main\Users;


use App\Entity\Main\Funds\Account;
use App\Entity\Main\Users\Role;
use App\Entity\Main\Users\User;
use App\Entity\Utils\ContributorFilter;
use App\Repository\Main\Funds\AccountRepository;
use App\Repository\Methods\RepositoryGeneralTrait;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    use RepositoryGeneralTrait;

    protected $alias = 'u';

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(UserInterface $user, string $newEncodedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newEncodedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    public function findInById(array $ids, array $conditions = []): array
    {
        $conditions = array_merge($conditions, ['in' => ['field' => 'id', 'values' => $ids]]);
        return $this->findByConditions($conditions);
    }

    /**
     * @return User[] Returns an array of User objects
     */
    public function findAdmins($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.role != :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            //->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }

    public function findAllForYear(DateTime $year)
    {
        return $this->createQueryBuilder('u')
            ->Where('u.createdAt <= :year')
            ->setParameter('year', $year)
            ->orderBy('u.createdAt', 'ASC')
            //->setMaxResults(10)
            ->getQuery()
            ->getResult();
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
        /** @var AccountRepository $accountRepo */
        $accountRepo = $this->_em->getRepository(Account::class);
        $accounts = $accountRepo->findWithFilter($filter);
        return array_map(fn ($account) => $account->getUser(), $accounts);
    }


    /*
    public function findOneBySomeField($value): ?User
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
