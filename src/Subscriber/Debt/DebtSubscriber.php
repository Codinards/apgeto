<?php

namespace App\Subscriber\Debt;

use App\Entity\Interests\UserInterest;
use App\Entity\Main\Funds\Account;
use App\Entity\Main\Funds\Debt;
use App\Entity\Main\Users\User;
use App\Events\Debt\CreateDebtEvent;
use App\Events\Debt\DeleteDebtEvent;
use App\Events\Debt\DispatchCreatedDebtInterest;
use App\Events\Debt\UpdateDebtEvent;
use App\Exception\AppException;
use App\Repository\Main\Funds\AccountRepository;
use App\Repository\Main\Funds\DebtRepository;
use App\Subscriber\AbstractEvent;
use App\Tools\AppConstants;
use Doctrine\ORM\EntityManagerInterface;

class DebtSubscriber extends AbstractEvent
{
    private bool $isDeleting = false;

    private bool $isCreating = false;

    private bool $isNormal = true;

    private bool $isUpdatedBalance = false;

    private bool $isUpdatedCreatedAt = false;

    private ?Debt $previous = null;

    public function __construct(
        private AccountRepository $accountRepository,
        private DebtRepository $debtRepository,
        private EntityManagerInterface $manager
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            DispatchCreatedDebtInterest::class => "onDispatchInterest",
            CreateDebtEvent::class => "onCreating",
            UpdateDebtEvent::class => "onUpdating",
            DeleteDebtEvent::class => "onDeleting"
        ];
    }

    public function onCreating(CreateDebtEvent $event)
    {
        $this->isDeleting = false;
        $this->isCreating = true;
        $debt = $event->getEntity();
        $this->isNormal = $debt->getLinkedDebtRenewal() !== null; // $debt->origin === Debt::FROM_DEBT;
        if ($debt->isInflow() and $this->isNormal === true) {
            throw new AppException("This event does not support a loan in flows operation");
        }
        $this->handleCreatingOrDeleting($debt);
        if ($debt->isInflow() === false) {
            $debt->resetLoanInFlows(0);
        }
        $this->isDeleting = false;
        $this->isNormal = true;
    }

    public function onDeleting(DeleteDebtEvent $event)
    {

        $this->isDeleting = true;
        $debt = $event->getEntity();
        $this->handleCreatingOrDeleting($debt);
        $this->isDeleting = false;
    }

    public function onUpdating(UpdateDebtEvent $event)
    {
        $this->isDeleting = false;
        $debt = $event->getEntity();
        $previous = $event->getPrevious();
        $this->previous = $previous;

        if (
            $previous->getCreatedAt() !== $debt->getCreatedAt()
            || $previous->getLoanBalances() !== $debt->getLoanBalances()
        ) {
            $this->isUpdatedBalance = $previous->getLoanBalances() !== $debt->getLoanBalances();
            $this->isUpdatedCreatedAt = $previous->getCreatedAt() !== $debt->getCreatedAt();
            $emptyDebt = new class(
                $previous->getUser(),
                (int) $previous->getPreviousBalances(),
                (int) $previous->getPreviousTotalInflows(),
                (int) $previous->getPreviousTotalOutflows(),
                (int) $previous->getLoanInFlows(),
                (int) $previous->getLoanOutFlows(),
            ) extends Debt
            {
                public function __construct(
                    private User $user,
                    private int $previousBalances,
                    private int $previousTotalInflows,
                    private int $previousTotalOutflows,
                    private int $loanInFlows,
                    private int $loanOutFlows
                ) {
                }
            };

            if ($previous->getCreatedAt() < $debt->getCreatedAt()) {
                $anterior = $this->debtRepository
                    ->findWhereCreatedAt(
                        $previous->getCreatedAt(),
                        operator: "<",
                        conditions: ["user" => $debt->getUser()],
                        getOneResult: true
                    ) ?? $emptyDebt;


                /** @var Debt[] $debts */
                $debts = $this->debtRepository
                    ->findWhereCreatedAt(
                        $previous->getCreatedAt(),
                        conditions: ["user" => $debt->getUser(), "id" => [$debt->getId(), "<>"]],
                        orderDesc: false
                    );
            } else {
                $anterior = $this->debtRepository
                    ->findWhereCreatedAt(
                        $debt->getCreatedAt(),
                        operator: "<",
                        conditions: ["user" => $debt->getUser()],
                        getOneResult: true
                    ) ?? $emptyDebt;

                /** @var Debt[] $debts */
                $debts = $this->debtRepository
                    ->findWhereCreatedAt(
                        $debt->getCreatedAt(),
                        conditions: ["user" => $debt->getUser(), "id" => [$debt->getId(), "<>"]],
                        orderDesc: false
                    );
            }

            $this->updateNexts($debt, $anterior, $debts);
        }
    }

    public function onDispatchInterest(DispatchCreatedDebtInterest $event)
    {
        // $debt = $event->getDebt();
        // $debtInterest = $event->getDebtInterest();
        // /** @var Account[]|Collection */
        // $interestBeneficiaries = $this->collection(
        //     $this->accountRepository
        //         ->findWhere('fund', 'supeq', AppConstants::$ACCOUNT_BASE_AMOUNT_TO_BENEFIT_INTEREST)
        // );
        // $accountSum = $interestBeneficiaries->sum(
        //     fn (Account $account) => $account->getCashBalances()
        // );
        // $debtSum = $this->collection(
        //     $this->accountRepository->findByConditions(['select' => 'loanBalances'])
        // )->sum(fn ($item) => $item['loanBalances']);

        // foreach ($interestBeneficiaries as $beneficiary) {
        //     $userInterest = (new UserInterest())
        //         ->setUser($beneficiary->getUser())
        //         ->setAccountBalance($beneficiary->getCashBalances())
        //         ->setDebtBalance($beneficiary->getLoanBalances())
        //         ->setDebt($debt)
        //         ->setAccountInterest(
        //             (int)($debtInterest->getAccountInterest() * $beneficiary->getCashBalances() / $accountSum)
        //         )
        //         ->setDebtInterest(
        //             (int)($debtInterest->getDebtInterest() * $beneficiary->getLoanBalances() / $debtSum)
        //         )
        //         ->setMonth($debt->getCreatedAt()->format('m'))
        //         ->setYear($debt->getCreatedAt()->format('Y'));

        //     $this->manager->persist($userInterest);
        // }
    }

    private function updateNexts(Debt &$debt, Debt $anterior, array &$debts = [])
    {
        $fakeAccount = (new Account())
            ->setUser($debt->getUser())
            ->setLoanBalances((int)$anterior->getPreviousBalances())
            ->resetLoanInFlows((int)$anterior->getPreviousTotalInflows())
            ->resetLoanOutFlows((int)$anterior->getPreviousTotalOutflows())
            ->setLoanOutFlows((int)$anterior->getLoanOutFlows())
            ->setLoanInFlows((int)$anterior->getLoanInFlows());

        if ($this->isDeleting === false) {
            array_unshift($debts, $debt);
        }

        $debts = $this->collection($debts)
            ->sortBy(fn (Debt $debt) => $debt->getCreatedAt());
        $after = null;
        /** @var Debt $previous */
        $debtClone = null;
        /** @var Debt $after */
        foreach ($debts as $after) {
            $debtClone = clone $after;

            $after
                ->setPreviousBalances((int)$fakeAccount->getLoanBalances())
                ->setPreviousTotalOutflows((int)$fakeAccount->getLoanOutFlows())
                ->setLoanBalances((int)$fakeAccount->getLoanBalances())
                ->setPreviousTotalInflows((int)$fakeAccount->getLoanInflows());

            if ($after->isInflow()) {
                if ($after->isDebtRenewalLinked() || !$this->isNormal) {

                    if ($debt === $after) {

                        if ($this->isUpdatedBalance && $this->isUpdatedCreatedAt) {
                            $inflow = $after->getLoanInFlows() - ($this->previous->getLoanBalances() - $this->previous->getLoanInFlows());
                        } elseif ($this->isUpdatedBalance) {
                            $inflow = $after->getLoanInFlows() - $fakeAccount->getLoanBalances();
                        } else {
                            $inflow = $this->previous?->getLoanInFlows()  ?? $after->getLoanInFlows();
                        }
                    } else {
                        $inflow = $after->getLoanInFlows();
                    }
                    $after->setLoanInFlows((int)$inflow);
                    $after->resetLoanInFlows((int)$inflow);
                } else {
                    $after->setLoanInFlows((int)$after->getLoanInFlows());
                }
                $after->resetLoanOutFlows(0);
            } else {
                $after->setLoanOutFlows((int)$after->getLoanOutFlows());
                $after->resetLoanInFlows(0);
            }

            // if ($this->isNormal === false || $after->isDebtRenewalLinked()) {

            //     if (!$this->isCreating && !$this->isDeleting and $after->getId() === $debt->getId()) {
            //         $after->resetLoanInFlows($debtClone->getLoanInFlows() - $debtClone->getPreviousBalances());
            //         $after->setLoanBalances($anterior->getLoanBalances() + $debtClone->getLoanInFlows());
            //     } else {
            //         $after->resetLoanInFlows($after->getLoanInFlows() - $after->getPreviousBalances());
            //     }

            //     $this->isNormal = true;
            // }

            if ($after->isInflow()) {
                $fakeAccount->setLoanInFlows(
                    (int)$after->getLoanInflows()
                );
            } else {
                $fakeAccount->setLoanOutFlows(
                    (int)$after->getLoanOutFlows()
                );
            }
            $previous = $after;
        }

        if ($after) {
            $after->getAccount()
                ->resetLoanOutFlows($fakeAccount->getLoanOutFlows())
                ->resetLoanInFlows($fakeAccount->getLoanInFlows())
                ->setLoanBalances($fakeAccount->getLoanBalances());
            unset($fakeAccount);
        } else {
            if ($this->isDeleting) {
                $debt->getAccount()
                    ->setLoanOutFlows(-$debt->getLoanOutFlows())
                    ->setLoanBalances($debt->getPreviousBalances());
            }
        }

        return $debt;
    }

    private function handleCreatingOrDeleting(Debt &$debt)
    {
        $anterior = $this->debtRepository
            ->findWhereCreatedAt(
                $debt->getCreatedAt(),
                operator: "<",
                conditions: ["user" => $debt->getUser()],
                getOneResult: true
            ) ?? new class(
                $debt->getUser(),
                0,
                0,
                0,
                0,
                0,
            ) extends Debt
            {
                public function __construct(
                    private User $user,
                    private int $previousBalances,
                    private int $previousTotalInflows,
                    private int $previousTotalOutflows,
                    private int $loanInFlows,
                    private int $loanOutFlows
                ) {
                }
            };

        /** @var Debt[] $debts */
        $debts = $this->debtRepository
            ->findWhereCreatedAt(
                $debt->getCreatedAt(),
                conditions: $this->isDeleting ? ["user" => $debt->getUser(), "id" => [$debt->getId(), "<>"]]
                    : ["user" => $debt->getUser()],
                orderDesc: false
            );

        return $this->updateNexts($debt, $anterior, $debts);
    }
}
