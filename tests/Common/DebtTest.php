<?php

namespace App\Tests\Common;

use App\Controller\Backend\Accounts\DebtController;
use App\Entity\Main\Funds\Account;
use App\Entity\Main\Funds\Debt;
use App\Entity\Main\Funds\DebtInterest;
use App\Entity\Main\Funds\Fund;
use App\Entity\Main\Users\Role;
use App\Entity\Main\Users\User;
use App\Tools\Entity\BaseRoleProperties;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class DebtTest extends KernelTestCase
{
    /**
     * @var Role[]
     */
    private array $roles = [];

    /**
     * @var User[]
     */
    private array $users = [];

    private EntityManagerInterface $manager;

    private ?\Faker\Generator $faker = null;

    public function setUp(): void
    {
        if ($this->faker === null) {
            $this->faker = \Faker\Factory::create();
        }
    }

    public function testIsTestEnv(): void
    {
        $kernel = self::bootKernel();

        $this->assertSame('test', $kernel->getEnvironment());
    }

    public function testDeleteDebt()
    {
        self::bootKernel();
        /** @var DebtController $debtController */
        $debtController = self::$container->get(DebtController::class);
        $this->manager = $debtController->getManager();
        // $this->dump($debtController->getRoleRepository()->findAll());
        $this->assertInstanceOf(DebtController::class, $debtController);
        $this->makeRole();
        $this->getUsers();
        $this->assertCount(11, $accounts = $debtController->getAccountRepository()->findAll());

        $this->makeFundAndDebt();
    }

    private function getUsers(int $count = 10): array
    {
        if (empty($this->users)) {
            $admin = (new User)
                ->setUsername("Admin")
                ->setPseudo("Admin")
                ->setPassword("mot2passe")
                ->setRole($this->roles[2]);
            $this->manager->persist($admin);
            $admin->setAdmin($admin);
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

    public function makeFundAndDebt()
    {
        $dates = ["2022-01-01", "2022-01-08", "2022-01-15", "2022-01-22"];
        $accounts = $this->manager->getRepository(Account::class)->findOneBy(['user' => $this->getUsers()]);
        foreach ($dates as $date) {
            $createdAt = new \DateTime($date);
            $year = "2022";
            /** @var Account $account */
            foreach ($accounts as $account) {
                foreach ([1, 2, 3] as $level) {
                    $inflow = (new Fund)
                        ->setCashBalances($account->getCashBalances())
                        ->setPreviousBalances(0)
                        ->setCashInFlows(rand(1, 150) * 10000)
                        ->setUser($account->getUser())
                        ->setAdmin($this->admin)
                        ->setWording('Solde initial')
                        ->setCreatedAt($createdAt)
                        ->setAccount($account)
                        ->setYear($year)
                        ->setObservations('Solde initial au ' . $createdAt->format('d-m-Y'));
                    $account->setCashInFlows($inflow->getCashInFlows());
                    $this->manager->persist($inflow);

                    if ($level > 2 and rand(1, 3) == 2) {
                        $outflow = (new Fund)
                            ->setCashBalances($account->getCashBalances())
                            ->setPreviousBalances($account->getCashBalances())
                            ->setCashOutFlows((int)($account->getCashBalances() * rand(1, 3) / 4))
                            ->setUser($account->getUser())
                            ->setAdmin($this->admin)
                            ->setWording('Retrait de fond')
                            ->setCreatedAt($createdAt)
                            ->setAccount($account)
                            ->setYear($year)
                            ->setObservations('Sortie en date du ' . $createdAt->format('d-m-Y'));
                        $account->setCashOutFlows($outflow->getCashOutFlows());
                        $this->manager->persist($outflow);
                    }
                }
            }
        }
        // foreach ($this->getUsers() as $user) {
        //     /** @var Account $account */
        //     $account = $this->manager->getRepository(Account::class)->findOneBy(['user' => $user]);
        //     if ($account) {
        //         $createdAt = $this->faker->dateTime();
        //         $year = $createdAt->format('Y');
        //         if (rand(1, 3) === 2) {
        //             $amount = (rand(100, 2000) * 1000 * rand(3, 4)) / 2;
        //             $interest = (int) $amount / 10;
        //             $debt = (new Debt)
        //                 ->setLoanBalances($account->getLoanBalances())
        //                 ->setPreviousBalances(0)
        //                 ->setUser($user)
        //                 ->setAdmin($this->admin)
        //                 ->setWording('Pret ' . $user->getName())
        //                 ->setCreatedAt($createdAt)
        //                 ->setAccount($account)
        //                 ->setYear($year)
        //                 ->setObservations(
        //                     '{"observations":"Avalisé par le fond propre", "firstAvaliste":{"avaliste":"'
        //                         . $user->getName()
        //                         . '", "observation":"Contre avalisé par la tontine 100000"}'
        //                 )
        //                 ->addAvaliste($user)
        //                 ->setLoanInFlows($amount)
        //                 ->setInterests($interest);
        //             $account->setLoanInFlows($amount);
        //             $debtInterest = (new DebtInterest())
        //                 ->setDebt($debt)
        //                 ->setWording($debt->getWording())
        //                 ->setInterest($interest)
        //                 ->setCreatedAt($createdAt)
        //                 ->setYear($year)
        //                 ->setIsRenewal(false);
        //             $this->manager->persist($debt);
        //             $account->setCurrentDebt($debt);
        //             $this->manager->persist($debtInterest);
        //         }


        //         $inflow = (new Fund)
        //             ->setCashBalances($account->getCashBalances())
        //             ->setPreviousBalances(0)
        //             ->setCashInFlows(rand(1, 150) * 10000)
        //             ->setUser($user)
        //             ->setAdmin($this->admin)
        //             ->setWording('Solde initial')
        //             ->setCreatedAt($createdAt)
        //             ->setAccount($account)
        //             ->setYear($year)
        //             ->setObservations('Solde initial au ' . $createdAt->format('d-m-Y'));
        //         $account->setCashInFlows($inflow->getCashInFlows());
        //         $this->manager->persist($inflow);

        //         $outflow = (new Fund)
        //             ->setCashBalances($account->getCashBalances())
        //             ->setPreviousBalances($account->getCashBalances())
        //             ->setCashOutFlows((int)($inflow->getCashBalances() * rand(1, 3) / 4))
        //             ->setUser($user)
        //             ->setAdmin($this->admin)
        //             ->setWording('Retrait de fond')
        //             ->setCreatedAt($createdAt)
        //             ->setAccount($account)
        //             ->setYear($year)
        //             ->setObservations('Sortie en date du ' . $createdAt->format('d-m-Y'));
        //         $account->setCashOutFlows($outflow->getCashOutFlows());
        //         $this->manager->persist($outflow);
        //     }
        // }
    }

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
