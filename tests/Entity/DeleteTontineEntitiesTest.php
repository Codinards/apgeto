<?php

namespace App\Tests\Entity;

use App\Controller\Backend\Accounts\AccountController;
use App\Controller\BaseController;
use App\Entity\Main\Funds\Account;
use App\Entity\Main\Users\Role;
use App\Entity\Main\Users\User;
use App\Entity\Tontines\Tontine;
use App\Entity\Tontines\Tontinetype;
use App\Entity\Tontines\TontineurData;
use App\Entity\Tontines\Unity;
use App\Tools\Entity\BaseRoleProperties;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

class DeleteTontineEntitiesTest extends KernelTestCase
{
    /**
     * @var Role[]
     */
    private array $roles = [];

    protected ?User $admin = null;

    /**
     * @var User[]
     */
    private array $users = [];

    private ?EntityManagerInterface $manager = null;

    // public function setUp(): void
    // {
    //     // $this->kernel = self::bootKernel();
    //     /** @var DebtController $debtController */
    //     $controller = self::$container->get(BaseController::class);
    //     $this->manager = $controller->getManager();
    // }

    public function testSomething(): void
    {
        $kernel = self::bootKernel();

        $this->assertSame('test', $kernel->getEnvironment());
        //$routerService = self::$container->get('router');
        //$myCustomService = self::$container->get(CustomService::class);
    }

    public function testdeleteTontine()
    {
        $kernel = self::bootKernel();
        /** @var AccountController $controller */
        $controller = self::$container->get(AccountController::class);
        $this->manager = $controller->getManager();
        $this->makeRole();
        $this->getUsers();

        /** users creation test */
        $this->assertCount(11, $controller->getAccountRepository()->findAll());


        /** Tontine type and Tontine creation test */
        $type = (new Tontinetype())
            ->setName("Tontine Test")
            ->setCotisation(10000)
            ->setHasAvaliste(false);

        $this->manager->persist($type);
        $tontine1 = (new Tontine)
            ->setType($type)
            ->setCotisation(0)
            ->setAmount(5000)
            ->setAdmin($this->admin);
        $tontine2 = (new Tontine)
            ->setType($type)
            ->setCotisation(0)
            ->setAmount(10000)
            ->setAdmin($this->admin);
        $this->manager->persist($tontine1);
        $this->manager->persist($tontine2);
        $this->manager->flush();
        $this->assertCount(1, $controller->getTontinetypeRepository()->findAll());
        $this->assertCount(2, $controller->getTontineRepository()->findAll());


        /** TontineurData and Unity creation test */
        /** @var TontineurData[] $data */
        $data = [];
        $tontineurs = $controller->getTontineurRepository()->findAll();
        foreach ($tontineurs as $item) {
            $tontineurdata = (new TontineurData)
                ->setTontine($tontine1)
                ->setTontineur($item);
            $tontineurdata->mergeUnities(1000, $this->admin, 2);
            /** @var Unity $unity */
            foreach ($tontineurdata->getUnities() as $unity) {
                $this->manager->persist($unity);
            }
            $this->manager->persist($tontineurdata);

            $tontineurdata2 = (new TontineurData)
                ->setTontine($tontine2)
                ->setTontineur($item);
            $tontineurdata2->mergeUnities(1500, $this->admin);
            foreach ($tontineurdata2->getUnities() as $unity2) {
                $this->manager->persist($unity2);
            }
            $this->manager->persist($tontineurdata2);
        }
        $this->manager->flush();

        $this->assertCount(22, $controller->getTontineurDataRepository()->findAll());
        $this->assertCount(33, $controller->getUnityRepository()->findAll());
        $this->assertCount(22, $unities = $controller->getUnityRepository()->findBy(["tontine" => $tontine1]));
        $this->assertCount(11, $controller->getUnityRepository()->findBy(["tontine" => $tontine2]));
        $this->assertCount(2, $tontineurdata->getUnities());

        /** Unity deleting test */
        $unity = $controller->getUnityRepository()->find($unity->getId());
        /** @var TontineurData $tontineurdata */
        $tontineurdata = $controller->getTontineurDataRepository()->findByConditions(
            ["memberOf" => ["unities" => $unity]]
        )[0];
        /** @var Unity $unity */
        $unity = $tontineurdata->getUnities()->first();
        $this->assertCount(2, $tontineurdata->getUnities());
        $unity->delete($this->manager);
        $this->manager->flush();
        $this->assertCount(1, $tontineurdata->getUnities());
        $this->assertCount(21, $controller->getUnityRepository()->findBy(["tontine" => $tontine1]));
        $this->assertCount(22, $controller->getTontineurDataRepository()->findAll());

        /** @var Unity $unity */
        $unity = $tontineurdata->getUnities()->first();
        $this->assertCount(1, $tontineurdata->getUnities());
        $unity->delete($this->manager);
        $this->manager->flush();
        $this->assertCount(1, $tontineurdata->getUnities());
        $this->assertCount(20, $controller->getUnityRepository()->findBy(["tontine" => $tontine1]));
        $this->assertCount(10, $controller->getTontineurDataRepository()->findBy(["tontine" => $tontine1]));
        $this->assertCount(21, $controller->getTontineurDataRepository()->findAll());

        /** Unity and TontineurData deleting test */
        $tontineurdata2 = $controller->getTontineurDataRepository()->findByConditions(
            ["memberOf" => ["unities" => $unity2]]
        )[0];
        $unity2 = $tontineurdata2->getUnities()->first();
        $unity2->delete($this->manager);
        $this->manager->flush();
        $this->assertCount(10, $controller->getTontineurDataRepository()->findBy(["tontine" => $tontine1]));
        $this->assertCount(10, $controller->getTontineurDataRepository()->findBy(["tontine" => $tontine2]));
        $this->assertCount(10, $controller->getUnityRepository()->findBy(["tontine" => $tontine2]));
        $this->assertCount(20, $controller->getTontineurDataRepository()->findAll());

        /** Tontine test deleting */
        $tontines = $controller->getTontineRepository()->findAll();
        $tontine1 = $tontines[0];
        $tontine1->delete($this->manager);
        $this->manager->flush();
        $this->assertCount(1, $controller->getTontineRepository()->findAll());
        $this->assertCount(10, $controller->getTontineurDataRepository()->findAll());
        $this->assertCount(10, $controller->getUnityRepository()->findAll());

        $tontine2 = $tontines[1];
        $tontine2->delete($this->manager);
        $this->manager->flush();
        $this->assertCount(0, $controller->getTontineRepository()->findAll());
        $this->assertCount(0, $controller->getTontineurDataRepository()->findAll());
        $this->assertCount(0, $controller->getUnityRepository()->findAll());
    }

    protected function getUsers(int $count = 10): array
    {
        if (empty($this->users)) {
            $admin = (new User)
                ->setUsername("Admin")
                ->setPseudo("Admin")
                ->setPassword("mot2passe")
                ->setRole($this->roles[2]);
            $this->manager->persist($admin);
            $admin->setAdmin($admin);
            $this->admin = $admin;
            $users = array_map(
                fn (int $index) => (new User())
                    ->setUsername("Test User $index")
                    ->setPassword("mot2passe")
                    ->setAdmin($admin)
                    ->setRole($this->roles[0]),
                range(1, 10)
            );
            $this->users = $users;
            foreach ($users as $user) {
                $this->manager->persist($user);
            }
            $this->manager->flush();
        }
        return $this->users;
    }

    // public function makeFundAndDebt()
    // {
    //     foreach ($this->getUsers() as $user) {
    //         /** @var Account $account */
    //         $account = $this->manager->getRepository(Account::class)->findOneBy(['user' => $user]);
    //         if ($account) {
    //             $createdAt = new \DateTime();
    //             $year = $createdAt->format('Y');
    //             if (rand(1, 3) === 2) {
    //                 $amount = (rand(100, 2000) * 1000 * rand(3, 4)) / 2;
    //                 $interest = (int) $amount / 10;
    //                 $debt = (new Debt)
    //                     ->setLoanBalances($account->getLoanBalances())
    //                     ->setPreviousBalances(0)
    //                     ->setUser($user)
    //                     ->setAdmin($this->admin)
    //                     ->setWording('Pret ' . $user->getName())
    //                     ->setCreatedAt($createdAt)
    //                     ->setAccount($account)
    //                     ->setYear($year)
    //                     ->setObservations(
    //                         '{"observations":"Avalisé par le fond propre", "firstAvaliste":{"avaliste":"'
    //                             . $user->getName()
    //                             . '", "observation":"Contre avalisé par la tontine 100000"}'
    //                     )
    //                     ->addAvaliste($user)
    //                     ->setLoanInFlows($amount)
    //                     ->setInterests($interest);
    //                 $account->setLoanInFlows($amount);
    //                 $debtInterest = (new DebtInterest())
    //                     ->setDebt($debt)
    //                     ->setWording($debt->getWording())
    //                     ->setInterest($interest)
    //                     ->setCreatedAt($createdAt)
    //                     ->setYear($year)
    //                     ->setIsRenewal(false);
    //                 $this->manager->persist($debt);
    //                 $account->setCurrentDebt($debt);
    //                 $this->manager->persist($debtInterest);
    //             }


    //             $inflow = (new Fund)
    //                 ->setCashBalances($account->getCashBalances())
    //                 ->setPreviousBalances(0)
    //                 ->setCashInFlows(rand(1, 150) * 10000)
    //                 ->setUser($user)
    //                 ->setAdmin($this->admin)
    //                 ->setWording('Solde initial')
    //                 ->setCreatedAt($createdAt)
    //                 ->setAccount($account)
    //                 ->setYear($year)
    //                 ->setObservations('Solde initial au ' . $createdAt->format('d-m-Y'));
    //             $account->setCashInFlows($inflow->getCashInFlows());
    //             $this->manager->persist($inflow);

    //             $outflow = (new Fund)
    //                 ->setCashBalances($account->getCashBalances())
    //                 ->setPreviousBalances($account->getCashBalances())
    //                 ->setCashOutFlows((int)($inflow->getCashBalances() * rand(1, 3) / 4))
    //                 ->setUser($user)
    //                 ->setAdmin($this->admin)
    //                 ->setWording('Retrait de fond')
    //                 ->setCreatedAt($createdAt)
    //                 ->setAccount($account)
    //                 ->setYear($year)
    //                 ->setObservations('Sortie en date du ' . $createdAt->format('d-m-Y'));
    //             $account->setCashOutFlows($outflow->getCashOutFlows());
    //             $this->manager->persist($outflow);
    //         }
    //     }
    // }

    private function dump(...$vars)
    {
        dump("-------------------------------------------------------------------------------------");
        dump(...$vars);
        dump("-------------------------------------------------------------------------------------");
    }

    public function makeRole()
    {
        foreach ([
            ['name' => BaseRoleProperties::ROLE_USER, 'title' => BaseRoleProperties::USER_TITLE, 'is_root' => false],
            ['name' => BaseRoleProperties::ROLE_ADMIN, 'title' => BaseRoleProperties::ADMIN_TITLE, 'is_root' => false],
            ['name' => BaseRoleProperties::ROLE_SUPERADMIN, 'title' => BaseRoleProperties::SUPERADMIN_TITLE, 'is_root' => true],
        ] as $item) {
            $role = (new Role())
                ->setName($item['name'])
                ->setTitle($item['title'])
                ->setIsDeletable(false);
            $this->manager->persist($role);
            $this->roles[] = $role;
            // if ($item['is_root']) {
            //     $this->root = $role;
            // }
        }
        $this->manager->flush();
    }
}
