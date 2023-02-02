<?php

namespace App\DataFixtures;

use App\Entity\Assistances\Assistance;
use App\Entity\Assistances\AssistanceType;
use App\Entity\Assistances\Contributor;
use App\Entity\Main\Funds\Account;
use App\Entity\Main\Funds\Debt;
use App\Entity\Main\Funds\DebtInterest;
use App\Entity\Main\Funds\Fund;
use App\Entity\Main\Operations\Operation;
use App\Entity\Main\Operations\Type;
use App\Entity\Main\Users\Role;
use App\Entity\Main\Users\User;
use App\Entity\Tontines\Tontinetype;
use App\Events\Debt\DispatchCreatedDebtInterest;
use App\Tools\AppConstants;
use App\Tools\Entity\BaseRoleProperties;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Illuminate\Support\Collection;
use Psr\EventDispatcher\EventDispatcherInterface;

class AppFixtures extends Fixture
{

    // protected $doctrine;

    private Collection $users;
    private Collection $accounts;
    private Collection $funds;
    private Collection $debts;
    private ?User $admin = null;

    private Collection $collection;

    private Collection $otherCollection;

    private Collection $baseCollection;


    public function __construct(
        private ManagerRegistry $doctrine,
        private EventDispatcherInterface $dispatcher
    ) {
        // $this->doctrine = $doctrine;
        $aideDates = [];

        $this->users = new Collection();
        $this->accounts = new Collection();
        $this->funds = new Collection();
        $this->debts = new Collection();
        $baseCollection = (new Collection(require (dirname(dirname(__DIR__))) . "/test.php"))->sortBy(function ($item) {
            $date = explode("/", $item["date"]);
            try {
                return new \DateTime($date[2]  . "-" . $date[1] . "-" . $date[0]);
            } catch (\Exception | \Error $e) {
                dd($item);
            }
        });

        $this->otherCollection = new \Illuminate\Support\Collection([
            "aides" => new \Illuminate\Support\Collection(),
            "synergie" => new \Illuminate\Support\Collection()
        ]);

        $collection = $baseCollection->filter(function ($data) use ($aideDates) {
            if (isset($data["libelé"])) {
                if (
                    (stripos($data['libelé'], "Aide") === 0 or stripos($data['libelé'], "Jouissance") === 0)
                ) {
                    if ($this->otherCollection->get("aides")->get($data['libelé'])) {
                        $this->otherCollection->get("aides")->put($data['libelé'], array_merge($this->otherCollection->get("aides")->get($data['libelé']), [$data]));;
                        return false;
                    } else {
                        $this->otherCollection->get("aides")->put($data['libelé'], [$data]);
                        $aideDates[$data['libelé']] = $data["date"];
                        return false;
                    }
                } elseif (str_contains($data['libelé'], 'recouvrement synergie')) {
                    if ($this->otherCollection->get("synergie")->get($data['libelé'])) {
                        $this->otherCollection->get("synergie")->put($data['libelé'], array_merge($this->otherCollection->get("synergie")->get($data['libelé']), [$data]));
                        return false;
                    } else {
                        $this->otherCollection->get("synergie")->put($data['libelé'], [$data]);
                        $aideDates[$data['libelé']] = $data["date"];
                        return false;
                    }
                }
            }
            return $data;
        });
        $this->aideDates = $aideDates;
        $this->baseCollection = $collection;
        $this->collection = $collection
            ->groupBy(function ($item) {
                return $item["date"];
            })
            ->map(function (Collection $col) {
                return $col->groupBy(fn ($item) => $item["membre"])
                    ->map(function (Collection $el) {
                        return $el->map(function ($item) {
                            $item["id"] = $item['id'] - 1;
                            return $item;
                        })->keyBy("libelé");
                    });
            });
    }

    public function load(ObjectManager $manager)
    {
        $suplements = [
            'Djuikoo Leonie' => [
                "date" => "2021-08-07",
                "period" => "P5M"
            ],
            'Zebaze Pierre Marie' => [
                "date" => "2021-09-11",
                "period" => "P5M"
            ],
            'Tsobeng Bertin' => [
                "date" => "2021-07-26",
                "period" => "P5M"
            ],
            'Nguefack Marc' => [
                "date" => "2021-12-24",
                "period" => "P5M"
            ],
            'Bogning Kenfack Victorien' => [
                "date" => "2021-12-04",
                "period" => "P5M"
            ],
            'Donfack Hyppolite' => [
                "date" => "2021-08-20",
                "period" => "P1Y8M"
            ],
            'Ngoufack Pascaline' => [
                "date" => "2021-12-11",
                "period" => "P5M"
            ],
            'Azambou Blaise Pascal' => [
                "date" => "2020-02-08",
                "period" => "P3Y1M"
            ],
            'Jiatsa Arlette' => [
                "date" => "2021-09-25",
                "period" => "P5M"
            ],
            'Kana Sokeng Francis' => [
                "date" => "2021-12-31",
                "period" => "P5M"
            ],
            'Dongmo Elvis' => [
                "date" => "2021-12-04",
                "period" => "P5M"
            ],
            'Ngapgho Denise' => [
                "date" => "2021-09-24",
                "period" => "P5M"
            ],
            'Tiomo Donnatchi Severin' => [
                "date" => "2021-12-04",
                "period" => "P5M"
            ],
            'Nguimfack Nguimzap Jean' => [
                "date" => "2021-05-08",
                "period" => "P1Y10M",
            ],
            'Nguimezap Leonel' => [
                "date" => "2022-01-01",
                "period" => "P5M"
            ],
            'Tsobjio Olivier' => [
                "date" => "2021-10-09",
                "period" => "P5M"
            ],
            'Azambou Rochelle' => [
                "date" => "2021-04-10",
                "period" => "P1Y10M"
            ],
            'Nguimeya Vincent' => [
                "date" => "2021-10-02",
                "period" => "P5M"
            ],
            'Tsafack Gervais' => [
                "date" => "2021-10-02",
                "period" => "P1Y5M"
            ],
            'Kitio Jean Noel' => [
                "date" => "2017-07-15",
                "period" => "P6Y"
            ]
        ];

        /** @var Collection $initials  */
        $initials = $this->collection->get("01/01/2022");
        $names = $initials->keys();
        $date = new \DateTime("2022-01-01");
        $year = (int) $date->format("Y");

        echo "------------------------------------------------------";
        echo "Seeding roles data... \n";
        echo "------------------------------------------------------";
        foreach ([
            ['name' => BaseRoleProperties::ROLE_USER, 'title' => BaseRoleProperties::USER_TITLE, 'is_root' => false],
            ['name' => BaseRoleProperties::ROLE_ADMIN, 'title' => BaseRoleProperties::ADMIN_TITLE, 'is_root' => false],
            ['name' => BaseRoleProperties::ROLE_SUPERADMIN, 'title' => BaseRoleProperties::SUPERADMIN_TITLE, 'is_root' => true],
        ] as $item) {
            $role = (new Role())
                ->setName($item['name'])
                ->setTitle($item['title'])
                ->setIsDeletable(false);
            $manager->persist($role);
            if ($item['is_root']) {
                $roleAdmin = $role;
            }
            $roles[] = $role;
        }

        /*******************************************************************************/

        echo "Seeding users .......... \n";
        echo "------------------------------------------------------";
        foreach ($names as $name) {
            $user = (new User)
                ->setUsername($name)
                ->setCreatedAt($date)
                ->setUpdatedAt($date)
                ->setRole($roles[1]);
            if ($name == "NGUIMFACK NGUIMZAP JEAN") {
                $user->setRole($roleAdmin);
                $user
                    ->setPseudo("Superadmin")
                    ->setPassword("mot2passe");
                $this->admin = $user;
            }
            $manager->persist($user);
            $this->users[$user->getId()] = $user;
        }
        $manager->flush();


        /************************************************************************* */
        echo "Seeding accounts ...... \n";
        echo "------------------------------------------------------";
        foreach ($this->users as $key => $user) {
            $name = $names[$key];
            /** @var Account $account */
            $account = $manager->getRepository(Account::class)
                ->findOneBy(['user' => $user]);
            $data = $initials[$name]["Solde initial"] ?? null;
            if (!$account) {
                dd(["id" => $user->getId(), "name" => $user->getName()]);
            }
            if ($account) {
                $this->accounts[$user->getId()] = $account;
            }
            if ($account and $data) {
                $name = $user->getUsername();
                $inflow = (new Fund)
                    ->setCashBalances($account->getCashBalances())
                    ->setPreviousBalances(0)
                    ->setPreviousTotalInflows(0)
                    ->setPreviousTotalOutflows(0)
                    ->setCashInFlows(floor((float)$data["montant"]))
                    ->setUser($user)
                    ->setAdmin($this->admin)
                    ->setWording('Solde initial au '  . $date->format('d-m-Y'))
                    ->setCreatedAt($date)
                    ->setAccount($account)
                    ->setYear($year)
                    ->setObservations('Solde initial au ' . $date->format('d-m-Y'));
                $account->setCashInFlows($inflow->getCashInFlows());
                $manager->persist($inflow);

                if (($data = $initials[$name]["Prêt"] ?? null) and ((int) $data["montant"] > 0)) {
                    $amount = floor((float) $data["montant"]);
                    $supplementName = implode(" ", array_map("ucfirst", explode(" ", strtolower($name))));
                    $debt = (new Debt)
                        ->setLoanBalances($account->getLoanBalances())
                        ->setPreviousTotalinflows(0)
                        ->setPreviousTotaloutflows(0)
                        ->setPreviousBalances(0)
                        ->setUser($user)
                        ->setAdmin($this->admin)
                        ->setWording('Solde Prêt Anterieur ' . $user->getName())
                        ->setCreatedAt(new \DateTime($suplements[$supplementName]["date"]))
                        ->setRenewalPeriod(new \DateInterval(
                            $suplements[$supplementName]["period"]
                        ))
                        ->setAccount($account)
                        ->setYear($year)
                        ->setObservations(
                            '{"observations":"Enregistrement du prêt antérieur"'
                        )
                        ->addAvaliste($user)
                        ->setLoanInFlows($amount)
                        ->setInterests(0);
                    $debt->setPaybackAt(
                        (new \DateTime(
                            $debt->getCreatedAt()->format("Y-m-d")
                        ))->add($debt->getRenewalPeriod())
                    );
                    $account->setLoanInFlows($amount);
                    $manager->persist($debt);
                    $account->setCurrentDebt($debt);

                    unset($initials[$name]["Prêt"]);
                }

                unset($initials[$name]["Solde initial"]);
            }
        }
        /** @var User[] */
        $usersByName = $this->users->keyBy(fn (User $user) => $user->getName());

        foreach ($initials as $name => $userData) {
            foreach ($userData as $title => $data) {
                $title = $data["libelé"];
                if (!in_array($title, ["Prêt", "Solde Initial", "Solde initial"])) {

                    /** @var User $user */
                    $user = $usersByName[$name];
                    // $user = $this->users->get(
                    //     $this->users->search(fn (User $us) => $us->getName() === $name) ?? null,
                    //     null
                    // );
                    /** @var Account $account */
                    $account = $this->accounts->get($user->getId());
                    if ($account === null) {
                        dd($data, $this->accounts->keys());
                    }
                    if ($title === "Remboursement") {
                        $amount = floor((float) $data["montant"]);
                        $debt = (new Debt)
                            ->setLoanBalances($account->getLoanBalances())
                            ->setPreviousTotalinflows($account->getLoanInFlows())
                            ->setPreviousTotaloutflows($account->getLoanOutFlows())
                            ->setPreviousBalances($account->getLoanBalances())
                            ->setUser($user)
                            ->setAdmin($this->admin)
                            ->setWording('Remboursement au  ' . $data["date"])
                            ->setCreatedAt(new \DateTime(
                                implode("-", array_reverse(explode("/", $data["date"])))
                            ))
                            ->setAccount($account)
                            ->setYear($year)
                            ->setLoanOutFlows($amount);
                        $account->setLoanOutFlows($amount);
                        $manager->persist($debt);
                    } else {
                        if ($data["type"] === "Entrée") {
                            $inflow = (new Fund)
                                ->setCashBalances($account->getCashBalances())
                                ->setPreviousBalances($account->getCashBalances())
                                ->setPreviousTotalInflows($account->getCashInFlows())
                                ->setPreviousTotalOutflows($account->getCashOutFlows())
                                ->setCashInFlows(floor((float)$data["montant"]))
                                ->setUser($user)
                                ->setAdmin($this->admin)
                                ->setWording($data["libelé"])
                                ->setCreatedAt(new \DateTime(
                                    implode("-", array_reverse(explode("/", $data["date"])))
                                ))
                                ->setAccount($account)
                                ->setYear($year);
                            $account->setCashInFlows($inflow->getCashInFlows());
                            $manager->persist($inflow);
                        } else {
                            $outflow = (new Fund)
                                ->setCashBalances($account->getCashBalances())
                                ->setPreviousBalances($account->getCashBalances())
                                ->setPreviousTotalInflows($account->getCashInFlows())
                                ->setPreviousTotalOutflows($account->getCashOutFlows())
                                ->setCashOutFlows(floor((float)$data["montant"]))
                                ->setUser($user)
                                ->setAdmin($this->admin)
                                ->setWording($data["libelé"])
                                ->setCreatedAt(new \DateTime(
                                    implode("-", array_reverse(explode("/", $data["date"])))
                                ))
                                ->setAccount($account)
                                ->setYear($year);
                            $account->setCashOutFlows($outflow->getCashOutFlows());
                            $manager->persist($outflow);
                        }
                    }
                }
            }
        }
        unset($initials);
        unset($this->collection["01/01/2022"]);

        echo "------------------------------------------------------";
        echo "Creating assistance type... \n";
        echo "------------------------------------------------------";

        $newCollection = $this->baseCollection->filter(
            fn ($value) => $value['date'] !== "01/01/2022"
        );

        $assistanceTypes = [];
        foreach ([
            ["name" => "Aide Parent", "amount" => 500_000, "is_amount" => true],
            ["name" => "Aide Maladie", "amount" => 200_000, "is_amount" => true],
            ["name" => "Aide Jouissance", "amount" => 3_000, "is_amount" => false],
            ["name" => "Aide Volontaire", "amount" => null, "is_amount" => false]
        ] as $assistanceType) {
            $type = (new AssistanceType())
                ->setAmount($assistanceType["amount"])
                ->setName($assistanceType["name"])
                ->setIsAmount($assistanceType["is_amount"]);
            $manager->persist($type);
            $assistanceTypes[$type->getName()] = $type;
        }

        $manager->persist(
            (new Type)
                ->setName("Synergie Menoua")
                ->setAdmin($this->admin)
        );
        file_put_contents(
            "assistances.json",
            str_replace(["u00e9", "\\"], "", json_encode($this->otherCollection->get("aides")->toJson()))
        );
        file_put_contents(
            "synergie.json",
            str_replace(["u00e9", "\\"], "", json_encode($this->otherCollection->get("synergie")->toJson()))
        );
        echo "------------------------------------------------------";
        echo "Seeding Other Data... \n";
        echo "------------------------------------------------------";
        foreach ($newCollection as $data) {
            if (!isset($data["date"])) dd($data);
            $user = $usersByName[$data["membre"]];
            /** @var Account */
            $account = $this->accounts[$user->getId()];
            if ($account === null) {
                dd($user);
            }
            $amount = floor((float)$data["montant"]);
            if (isset($data["type"])) {
                if ($data["type"] === "Entrée") {
                    $outflow = (new Fund)
                        ->setCashBalances($account->getCashBalances())
                        ->setPreviousBalances($account->getCashBalances())
                        ->setPreviousTotalInflows($account->getCashInFlows())
                        ->setPreviousTotalOutflows($account->getCashOutFlows())
                        ->setCashInFlows($amount)
                        ->setUser($user)
                        ->setAdmin($this->admin)
                        ->setWording($data["libelé"])
                        ->setCreatedAt(new \DateTime(
                            implode("-", array_reverse(explode("/", $data["date"])))
                        ))
                        ->setAccount($account)
                        ->setYear($year);
                    $account->setCashInFlows($outflow->getCashInFlows());
                    $manager->persist($outflow);
                } elseif ($data["type"] === "Sortie") {
                    $outflow = (new Fund)
                        ->setCashBalances($account->getCashBalances())
                        ->setPreviousBalances($account->getCashBalances())
                        ->setPreviousTotalInflows($account->getCashInFlows())
                        ->setPreviousTotalOutflows($account->getCashOutFlows())
                        ->setCashOutFlows($amount)
                        ->setUser($user)
                        ->setAdmin($this->admin)
                        ->setWording($data["libelé"])
                        ->setCreatedAt(new \DateTime(
                            implode("-", array_reverse(explode("/", $data["date"])))
                        ))
                        ->setAccount($account)
                        ->setYear($year);
                    $account->setCashOutFlows($outflow->getCashOutFlows());
                    $manager->persist($outflow);
                }
                // } else {
                else if ($data["type"] === "Prêt") {
                    $debt = (new Debt)
                        ->setLoanBalances($account->getLoanBalances())
                        ->setPreviousTotalinflows($account->getLoanInFlows())
                        ->setPreviousTotaloutflows($account->getLoanOutFlows())
                        ->setPreviousBalances($account->getLoanBalances())
                        ->setUser($user)
                        ->setAdmin($this->admin)
                        ->setWording('Nouveau prêt ' . $user->getName())
                        ->setCreatedAt(new \DateTime(
                            implode("-", array_reverse(explode("/", $data["date"])))
                        ))
                        ->setRenewalPeriod(new \DateInterval(
                            "P0Y5M0D"
                        ))
                        ->setAccount($account)
                        ->setYear($year)
                        ->addAvaliste($user)
                        ->setLoanInFlows($amount)
                        ->setInterests((int) floor($amount * 0.1));
                    $debt->setPaybackAt(
                        (new \DateTime(
                            $debt->getCreatedAt()->format("Y-m-d")
                        ))->add($debt->getRenewalPeriod())
                    );
                    $account->setLoanInFlows($amount);
                    $manager->persist($debt);
                    $manager->persist(
                        $debtInterest = (new DebtInterest)
                            ->setWording($debt->getWording())
                            ->setInterest($debt->getInterests())
                            ->setCreatedAt($debt->getCreatedAt())
                            ->setDebt($debt)
                    );
                    $account->setCurrentDebt($debt);
                    $this->dispatcher->dispatch(
                        new DispatchCreatedDebtInterest($debt, $debtInterest)
                    );
                } else {
                    $debt = (new Debt)
                        ->setLoanBalances($account->getLoanBalances())
                        ->setPreviousTotalinflows($account->getLoanInFlows())
                        ->setPreviousTotaloutflows($account->getLoanOutFlows())
                        ->setPreviousBalances($account->getLoanBalances())
                        ->setUser($user)
                        ->setAdmin($this->admin)
                        ->setWording('Remboursement au  ' . $data["date"])
                        ->setCreatedAt(new \DateTime(
                            implode("-", array_reverse(explode("/", $data["date"])))
                        ))
                        ->setAccount($account)
                        ->setYear($year)
                        ->setLoanOutFlows($amount)
                        ->setParent($account->getCurrentDebt());
                    $account->setLoanOutFlows($amount);
                    $manager->persist($debt);
                }
            }
        }
        $manager->flush();
        echo "end of seed \n";
    }

    // public function load(ObjectManager $manager)
    // {
    //     $data = [
    //         'Djuikoo Theophile' => ["fund" => 2_482_800, "loan" => 0],
    //         'Azebaze Fidel' => ["fund" =>   47_674, "loan" => 0],
    //         'Djuikoo Leonie' => [
    //             "fund" => 3_398_800,
    //             "loan" => 4_568_204,
    //             "date" => "2021-08-07",
    //             "period" => "P5M"
    //         ],
    //         'Zebaze Pierre Marie' => [
    //             "fund" => 483_178 + 56_622,
    //             "loan" => 129_273,
    //             "date" => "2021-09-11",
    //             "period" => "P5M"
    //         ],
    //         'Tsobeng Bertin' => [
    //             "fund" => -58_971 + 25_371,
    //             "loan" => 1_155_526,
    //             "date" => "2021-07-26",
    //             "period" => "P5M"
    //         ],
    //         'Kamgou Fabien' => ["fund" => 4_301_302 + 200_498, "loan" => 0],
    //         'Tsopgni Bruno' => ["fund" => 103_580, "loan" => 0],
    //         'Nguefack Marc' => [
    //             "fund" => 847_699 + 113_301,
    //             "loan" => 1_608_000,
    //             "date" => "2021-12-24",
    //             "period" => "P5M"
    //         ],
    //         'Kitio Joseph' => ["fund" => 80_128, "loan" => 0],
    //         'Bogning Kenfack Victorien' => [
    //             "fund" => 260_657 + 61_143,
    //             "loan" => 590_000,
    //             "date" => "2021-12-04",
    //             "period" => "P5M"
    //         ],
    //         'Likemo Jacqueline' => ["fund" => 1_443_637 + 100_163, "loan" => 0],
    //         'Donfack Hyppolite' => [
    //             "fund" => 70_997,
    //             "loan" => 176_753,
    //             "date" => "2021-08-20",
    //             "period" => "P1Y8M"
    //         ],
    //         'Tsopgni Marie Claire' => ["fund" => 128_567, "loan" => 0],
    //         'Donfack Marie Madeleine' => ["fund" => 81_254, "loan" => 0],
    //         'Nguefack Carine' => ["fund" => 90_164, "loan" => 0],
    //         'Tsobeng Mireine' => ["fund" => 28_322, "loan" => 0],
    //         'Nguepi Demanou Rodrigue' => ["fund" => 97_580, "loan" => 0],
    //         'Bogning Jean Pierre' => ["fund" => 105_520 + 8_780, "loan" => 0],
    //         'Tiago Fabien' => ["fund" => 138_895, "loan" => 0],
    //         'Zebaze Yolande' => ["fund" => 430_644 + 35_156, "loan" => 0],
    //         'Azebaze Rufine' => ["fund" => 46_852, "loan" => 0],
    //         'Ngoufack Pascaline' => [
    //             "fund" => 752_916 + 124_884,
    //             "loan" => 1_850_000,
    //             "date" => "2021-12-11",
    //             "period" => "P5M"
    //         ],
    //         'Nguetsop Alvine' => ["fund" => 71816 - 7000, "loan" => 0],
    //         'Feujio Viviane' => ["fund" => 80_753 + 1_047, "loan" => 0],
    //         'Azambou Blaise Pascal' => [
    //             "fund" => 739_338 - 7000 + 141_462,
    //             "loan" => 1_496_523,
    //             "date" => "2020-02-08",
    //             "period" => "P3Y1M"
    //         ],
    //         'Jatsa Arlette' => [
    //             "fund" => 679_718 + 76_082,
    //             "loan" => 600_000,
    //             "date" => "2021-09-25",
    //             "period" => "P5M"
    //         ],
    //         'Djiofack Alain Thomas' => ["fund" => 557_134 + 212_666, "loan" => 0],
    //         'Donkeng Marie Yvette' => ["fund" => 95_815 + 3_985, "loan" => 0],
    //         'Kana Sokeng Francis' => [
    //             "fund" => 1_274_800,
    //             "loan" => 2_500_000,
    //             "date" => "2021-12-31",
    //             "period" => "P5M"
    //         ],
    //         'Voufo Louis Bertin' => ["fund" => 38_140, "loan" => 0],
    //         'Nguezang Pierre Roger' => ["fund" => 59_924, "loan" => 0],
    //         'Dongmo Elvis' => [
    //             "fund" => 349_567 + 35_933,
    //             "loan" => 33000,
    //             "date" => "2021-12-04",
    //             "period" => "P5M"
    //         ],
    //         'Ngapgho Denise' => [
    //             "fund" => 848_317 + 153_183,
    //             "loan" => 961_000,
    //             "date" => "2021-09-24",
    //             "period" => "P5M"
    //         ],
    //         'Kenfack Chantale' => ["fund" => 28_661, "loan" => 0],
    //         'Momo Leonie' => ["fund" => 72_695, "loan" => 0],
    //         'Tiomo Donnatchi Severin' => [
    //             "fund" => 827_200,
    //             "loan" => 2_676_000,
    //             "date" => "2021-12-04",
    //             "period" => "P5M"
    //         ],
    //         'Dongmo Eveline' => ["fund" => 48_275, "loan" => 0],
    //         'Kitio Alvine' => ["fund" => 123_752, "loan" => 0],
    //         'Kenfack Celestine' => ["fund" => 3_685, "loan" => 0],
    //         'Nguimenang Laure' => ["fund" => 41_562, "loan" => 0],
    //         'Nguimfack Nguimzap Jean' => [
    //             "fund" => 1_117_234 + 163_566,
    //             "loan" => 2_000_000,
    //             "date" => "2021-05-08",
    //             "period" => "P1Y10M",
    //             "admin" => true
    //         ],
    //         'Kemda Pierre' => ["fund" => 142_468, "loan" => 0],
    //         'Jatsa Louis' => ["fund" => 122_065 + 2_735, "loan" => 0],
    //         'Kana Albert' => ["fund" => 82_469, "loan" => 0],
    //         'Nguimzap Leonel' => [
    //             "fund" => 85_447 - 14_000,
    //             "loan" => 89_700,
    //             "date" => "2022-01-01",
    //             "period" => "P5M"
    //         ],
    //         'Donfack Gladice' => ["fund" => -1_374, "loan" => 0],
    //         'Tsobjio Olivier' => [
    //             "fund" => 27_500,
    //             "loan" => 800_000,
    //             "date" => "2021-10-09",
    //             "period" => "P5M"
    //         ],
    //         'Azambou Rochelle' => [
    //             "fund" => 819_397 - 7_000 + 115_403,
    //             "loan" => 1_400_000,
    //             "date" => "2021-04-10",
    //             "period" => "P1Y10M"
    //         ],
    //         'Nguena Alex' => ["fund" => 238_731 + 10_069, "loan" => 0],
    //         'Nguimeya Vincent' => [
    //             "fund" => 997_836 + 77_964,
    //             "loan" => 1_000_000,
    //             "date" => "2021-10-02",
    //             "period" => "P5M"
    //         ],
    //         'Djiatsa Simplice' => ["fund" => 18_842, "loan" => 0],
    //         'Bogning Edvige' => ["fund" => 100_193 - 3500, "loan" => 0],
    //         'Tsafack Gervais' => [
    //             "fund" => 1_939_831 + 131_969,
    //             "loan" => 5_000_000,
    //             "date" => "2021-10-02",
    //             "period" => "P1Y5M"
    //         ],
    //         'Nguepi Blaise' => ["fund" => 107_060 + 8_740, "loan" => 0],
    //         'Tsafack Iriane Vicky' => ["fund" => 771_560 + 56_840, "loan" => 0],
    //         'Kitio Elianne' => ["fund" => 72_283, "loan" => 0],
    //         'Kenfack Anne Alice' => ["fund" => 196_060 - 7_000 + 29_740, "loan" => 0],
    //         'Zebaze Annette' => ["fund" => 126_671 + 11_129, "loan" => 0],
    //         'Zebaze Nguimezap Roderick' => ["fund" => 116_860, "loan" => 0],
    //         'Nguekeng Kevine' => ["fund" => 1_251_640 + 81_160, "loan" => 0],
    //         'Nguegang Kenfack Reine Aimée' => ["fund" => 33_560, "loan" => 0],
    //         'Nguefack Marie' => ["fund" => 78_760, "loan" => 0],
    //         'Nguepi Nguedia Astride' => ["fund" => 82_960, "loan" => 0],
    //         'Nguimfack Kemwe Dorice' => ["fund" => 566_447 + 1_253, "loan" => 0],
    //         'Mbogning Virginie' => ["fund" => 32_425, "loan" => 0],
    //         'Feujio Sidonie' => ["fund" => 77_380, "loan" => 0],
    //         'Tsague Marie Pascale' => ["fund" => 80_360, "loan" => 0],
    //         'Mbogning Leontine' => ["fund" => 54_725 - 3500, "loan" => 0],
    //         'Kenfack Nguepi Albertine' => ["fund" => 73_360, "loan" => 0],
    //         'Jiomezi Songa Brachere' => ["fund" => 87_225, "loan" => 0],
    //         'Mezatio Chancelier' => ["fund" => 58_510 - 7_000, "loan" => 0],
    //         'Fogo Alex' => ["fund" => 112_800, "loan" => 0],
    //     ];

    //     /** @var User[] $users */
    //     $users = [];
    //     /** @var Debt[] $debts */
    //     $debts = [];
    //     /** @var Fund[] $funds */
    //     $funds = [];
    //     $roles = [];
    //     $year = 2022;
    //     $now = new \DateTime("2022-01-01");
    //     echo "------------------------------------------------------";
    //     echo "Seeding roles data... \n";
    //     echo "------------------------------------------------------";
    //     foreach ([
    //         ['name' => BaseRoleProperties::ROLE_USER, 'title' => BaseRoleProperties::USER_TITLE, 'is_root' => false],
    //         ['name' => BaseRoleProperties::ROLE_ADMIN, 'title' => BaseRoleProperties::ADMIN_TITLE, 'is_root' => false],
    //         ['name' => BaseRoleProperties::ROLE_SUPERADMIN, 'title' => BaseRoleProperties::SUPERADMIN_TITLE, 'is_root' => true],
    //     ] as $item) {
    //         $role = (new Role())
    //             ->setName($item['name'])
    //             ->setTitle($item['title'])
    //             ->setIsDeletable(false);
    //         $manager->persist($role);
    //         if ($item['is_root']) {
    //             $roleAdmin = $role;
    //         }
    //         $roles[] = $role;
    //     }

    //     echo "Seeding users data... \n";
    //     echo "------------------------------------------------------";
    //     foreach ($data as $name => $item) {
    //         $users[] = $user = (new User)
    //             ->setUsername($name)
    //             ->setCreatedAt($now)
    //             ->setUpdatedAt($now)
    //             ->setRole($roles[1]);
    //         if (isset($item["admin"])) {
    //             $user->setRole($roleAdmin);
    //             $user
    //                 ->setPseudo("Superadmin")
    //                 ->setPassword("mot2passe");
    //             $admin = $user;
    //         }
    //         $manager->persist($user);
    //     }
    //     $manager->flush();

    //     echo "Seeding accounts data... \n";
    //     echo "------------------------------------------------------";
    //     foreach ($users as $user) {
    //         $account = $manager->getRepository(Account::class)->findOneBy(['user' => $user]);
    //         $user->setAdmin($admin);
    //         if ($account) {
    //             $name = $user->getUsername();
    //             $inflow = (new Fund)
    //                 ->setCashBalances($account->getCashBalances())
    //                 ->setPreviousBalances(0)
    //                 ->setPreviousTotalInflows(0)
    //                 ->setPreviousTotalOutflows(0)
    //                 ->setCashInFlows($data[$name]["fund"])
    //                 ->setUser($user)
    //                 ->setAdmin($admin)
    //                 ->setWording('Solde initial au '  . $now->format('d-m-Y'))
    //                 ->setCreatedAt($now)
    //                 ->setAccount($account)
    //                 ->setYear($year)
    //                 ->setObservations('Solde initial au ' . $now->format('d-m-Y'));
    //             $account->setCashInFlows($inflow->getCashInFlows());
    //             $manager->persist($inflow);

    //             if (($amount = $data[$name]["loan"]) > 0) {
    //                 $debt = (new Debt)
    //                     ->setLoanBalances($account->getLoanBalances())
    //                     ->setPreviousBalances(0)
    //                     ->setUser($user)
    //                     ->setAdmin($admin)
    //                     ->setWording('Solde Prêt Anterieur ' . $user->getName())
    //                     ->setCreatedAt(new \DateTime($data[$name]["date"]))
    //                     ->setRenewalPeriod(new \DateInterval(
    //                         $data[$name]["period"]
    //                     ))
    //                     ->setAccount($account)
    //                     ->setYear($year)
    //                     ->setObservations(
    //                         '{"observations":"Enregistrement du prêt antérieur"'
    //                     )
    //                     ->addAvaliste($user)
    //                     ->setLoanInFlows($amount)
    //                     ->setInterests(0);
    //                 $debt->setPaybackAt(
    //                     (new \DateTime(
    //                         $debt->getCreatedAt()->format("Y-m-d")
    //                     ))->add($debt->getRenewalPeriod())
    //                 );
    //                 $account->setLoanInFlows($amount);
    //                 $manager->persist($debt);
    //                 $account->setCurrentDebt($debt);
    //             }
    //         }
    //     }
    //     $manager->flush();
    //     echo "end of seed \n";
    // }

    // public function load(ObjectManager $manager)
    // {
    // $roles = [];
    // foreach ([
    //     ['name' => BaseRoleProperties::ROLE_USER, 'title' => BaseRoleProperties::USER_TITLE, 'is_root' => false],
    //     ['name' => BaseRoleProperties::ROLE_ADMIN, 'title' => BaseRoleProperties::ADMIN_TITLE, 'is_root' => false],
    //     ['name' => BaseRoleProperties::ROLE_SUPERADMIN, 'title' => BaseRoleProperties::SUPERADMIN_TITLE, 'is_root' => true],
    // ] as $item) {
    //     $role = (new Role())
    //         ->setName($item['name'])
    //         ->setTitle($item['title'])
    //         ->setIsDeletable(false);
    //     $manager->persist($role);
    //     if ($item['is_root']) {
    //         $roleAdmin = $role;
    //     }
    //     $roles[] = $role;
    // }

    // $users = [];

    // $oldAdmins = $manager->getRepository(User::class)->findAll();
    // if (empty($oldAdmins)) {
    //     $admin = (new User)
    //         ->setUsername('Test Administrator')
    //         ->setPseudo('Superadmin')
    //         ->setRole($roleAdmin)
    //         ->setAddress('Santa Maria 2')
    //         ->setTelephone('222717171')
    //         ->setPassword('mot2passe');
    //     $manager->persist($admin);
    //     $admin->setAdmin($admin);
    // } else {
    //     $admin = $oldAdmins[0];
    // }

    // /** @var User[] $users */
    // $users[] = $admin;

    // $faker = Factory::create('fr_FR');
    // $admins = [$admin];
    // for ($i = 0; $i < 70; $i++) {
    //     $user = (new User)
    //         ->setAddress($faker->address)
    //         ->setTelephone($faker->phoneNumber)
    //         ->setPassword('mot2passe')
    //         ->setUsername($faker->firstName . ' ' . $faker->lastName);
    //     if ($i == 0) {
    //         $user
    //             ->setPseudo('Administrateur')
    //             ->setRole($roles[1]);
    //     } elseif ($i == 1) {
    //         $user
    //             ->setPseudo('Membre')
    //             ->setRole($roles[0]);
    //     } else {
    //         $user
    //             ->setPseudo($faker->firstName . ' ' . $i)
    //             ->setRole($roles[rand(0, 2)]);
    //     }
    //     if ($i < 3 and ($count = count($admins)) > 1) {
    //         $user->setAdmin($admins[rand(0, ($count - 1))]);
    //     } else {
    //         $user->setAdmin($admin);
    //     }
    //     if ($user->getRole()->getName() !== 'ROLE_USER') {
    //         $admins[] = $user;
    //     }
    //     $manager->persist($user);
    //     $users[] = $user;
    // }

    // foreach ([
    //     ['name' => 'Tontine 5000', 'cotisation' => 5000, 'minAchat' => 0, 'isCurrent' => true, 'hasAvaliste' => false, 'amend' => 5000, 'minAmend' => 2500, 'hasAchat' => false, 'hasMultipleTontine' => false],
    //     ['name' => 'Tontine 10000', 'cotisation' => 10000, 'minAchat' => 0, 'isCurrent' => true, 'hasAvaliste' => false, 'amend' => 10000, 'minAmend' => 5000, 'hasAchat' => true, 'hasMultipleTontine' => false],
    //     ['name' => 'Tontine 15000', 'cotisation' => 15000, 'minAchat' => 15000, 'isCurrent' => true, 'hasAvaliste' => true, 'amend' => 10000, 'minAmend' => 5000, 'hasAchat' => true, 'hasMultipleTontine' => true],
    //     ['name' => 'Tontine 20000', 'cotisation' => 20000, 'minAchat' => 25000, 'isCurrent' => true, 'hasAvaliste' => false, 'amend' => 20000, 'minAmend' => 10000, 'hasAchat' => true, 'hasMultipleTontine' => false],
    //     ['name' => 'Tontine 50000', 'cotisation' => 50000, 'minAchat' => 0, 'isCurrent' => true, 'hasAvaliste' => false, 'amend' => 5000, 'minAmend' => 5000, 'hasAchat' => false, 'hasMultipleTontine' => true]
    // ] as $item) {
    //     $type = new Tontinetype;
    //     foreach ($item as $key => $field) {
    //         $method = 'set' . ucfirst($key);
    //         $type->$method($field);
    //     }
    //     $manager->persist($type);
    // }

    // foreach ([
    //     ['name' => 'Aide Parent', 'amount' => 500_000, 'isAmount' => 0],
    //     ['name' => 'Aide Jouissance', 'amount' => 5_000, 'isAmount' => 1],
    //     ['name' => 'Aide Volontaire', 'amount' => null, 'isAmount' => null]
    // ] as $item) {
    //     $type = new AssistanceType;
    //     foreach ($item as $key => $field) {
    //         $method = 'set' . ucfirst($key);
    //         $type->$method($field);
    //     }
    //     $manager->persist($type);
    // }

    // // $types = $manager->getRepository(Type::class)
    // //     ->findBy(['name' => ['Account Operations', 'Debt Operations']]);
    // // if (empty($types)) {

    // //     $operationtypes = [
    // //         ['name' => 'Account Operations'],
    // //         ['name' => 'Debt Operations'],
    // //     ];
    // //     foreach ($operationtypes as $type) {
    // //         $manager->persist(
    // //             $type = (new Type)
    // //                 ->setName($type['name'])
    // //                 ->setIsUpdatable(false)
    // //                 ->setBalance(0)
    // //                 ->setAdmin($user)
    // //         );
    // //         $types[] = $type;
    // //     }
    // // }

    // $manager->flush();

    // // $fundType = $types[0];
    // // $debtType = $types[1];
    // foreach ($users as $user) {
    //     /** @var Account $account */
    //     $account = $manager->getRepository(Account::class)->findOneBy(['user' => $user]);
    //     if ($account) {
    //         $createdAt = new DateTime();
    //         $year = $createdAt->format('Y');
    //         if (rand(1, 3) === 2) {
    //             $amount = (rand(100, 2000) * 1000 * rand(3, 4)) / 2;
    //             $interest = (int) $amount / 10;
    //             $debt = (new Debt)
    //                 ->setLoanBalances($account->getLoanBalances())
    //                 ->setPreviousBalances(0)
    //                 ->setUser($user)
    //                 ->setAdmin($admins[rand(0, 2)])
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
    //             $manager->persist($debt);
    //             $account->setCurrentDebt($debt);
    //             $manager->persist($debtInterest);
    //         }


    //         $inflow = (new Fund)
    //             ->setCashBalances($account->getCashBalances())
    //             ->setPreviousBalances(0)
    //             ->setCashInFlows(rand(1, 150) * 10000)
    //             ->setUser($user)
    //             ->setAdmin($admins[rand(0, 2)])
    //             ->setWording('Solde initial')
    //             ->setCreatedAt($createdAt)
    //             ->setAccount($account)
    //             ->setYear($year)
    //             ->setObservations('Solde initial au ' . $createdAt->format('d-m-Y'));
    //         $account->setCashInFlows($inflow->getCashInFlows());
    //         $manager->persist($inflow);

    //         $outflow = (new Fund)
    //             ->setCashBalances($account->getCashBalances())
    //             ->setPreviousBalances($account->getCashBalances())
    //             ->setCashOutFlows((int)($inflow->getCashBalances() * rand(1, 3) / 4))
    //             ->setUser($user)
    //             ->setAdmin($admins[rand(0, 2)])
    //             ->setWording('Retrait de fond')
    //             ->setCreatedAt($createdAt)
    //             ->setAccount($account)
    //             ->setYear($year)
    //             ->setObservations('Sortie en date du ' . $createdAt->format('d-m-Y'));
    //         $account->setCashOutFlows($outflow->getCashOutFlows());
    //         $manager->persist($outflow);
    //     }
    // }
    // $manager->flush();
    // }
}
