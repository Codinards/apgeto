<?php

namespace App\Tools\Entity;

use App\Entity\Assistances\Assistance;
use App\Entity\Assistances\Contributor;
use App\Entity\Main\Funds\Account;
use App\Entity\Main\Funds\Fund;
use App\Entity\Main\Users\User;
use App\Entity\Tontines\Tontineur;
use App\Exception\AppException;
use App\Repository\Assistances\AssistanceRepository;
use App\Repository\Assistances\ContributorRepository;
use App\Repository\Main\Funds\AccountRepository;
use App\Repository\Main\Funds\FundRepository;
use App\Repository\Main\Users\UserRepository;
use App\Repository\Tontines\TontineurRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\Persistence\ManagerRegistry;
use Exception;

class StaticEntityManager
{
    protected $doctrine;

    protected static $instance;

    public function __construct(ManagerRegistry $doctrine)
    {

        $this->doctrine = $doctrine;
        self::$instance = $this;
    }

    public static function getManager(?string $name = null)
    {
        try {
            return self::$instance->doctrine->getManager($name);
        } catch (Exception $e) {
            throw new AppException($e->getMessage(), $e->getCode(), $e->getPrevious());
        }
    }

    public static function getDefaultManager(): EntityManager
    {
        return self::getManager('default');
    }

    public static function getTontineManager(): EntityManager
    {
        return self::getManager('tontine');
    }

    public static function getAssistanceManager(): EntityManager
    {
        return self::getManager('assistance');
    }

    public static function getInterestManager(): EntityManager
    {
        return self::getManager('interest');
    }

    public static function getTontineurRepository(): TontineurRepository
    {
        return self::getTontineManager()->getRepository(Tontineur::class);
    }

    public static function getUserRepository(): UserRepository
    {
        return self::getDefaultManager()->getRepository(User::class);
    }

    public static function getAccountRepository(): AccountRepository
    {
        return self::getDefaultManager()->getRepository(Account::class);
    }

    public static function getFundRepository(): FundRepository
    {
        return self::getDefaultManager()->getRepository(Fund::class);
    }

    public static function getAssistanceRepository(): AssistanceRepository
    {
        return self::getAssistanceManager()->getRepository(Assistance::class);
    }

    public static function getContributorRepository(): ContributorRepository
    {
        return self::getAssistanceManager()->getRepository(Contributor::class);
    }
}
