<?php

namespace App\Subscriber\Fund;

use App\Entity\Main\Funds\Account;
use App\Entity\Main\Funds\Fund;
use App\Entity\Main\Users\User;
use App\Events\Fund\CreateFundEvent;
use App\Events\Fund\DeleteFundEvent;
use App\Events\Fund\DeleteLinkedDebtRenewalEvent;
use App\Events\Fund\UpdateFundEvent;
use App\Events\Fund\UpdateLinkedDebtRenewalEvent;
use App\Repository\Main\Funds\FundRepository;
use App\Subscriber\AbstractEvent;
use Psr\EventDispatcher\EventDispatcherInterface;

class FundSubscriber extends AbstractEvent
{
    private bool $isDeleting = false;

    public function __construct(
        private FundRepository $fundRepository,
        private EventDispatcherInterface $dispatcher
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            CreateFundEvent::class => "onCreate",
            UpdateFundEvent::class => "onUpdating",
            DeleteFundEvent::class => "onDelete",
            UpdateLinkedDebtRenewalEvent::class => "onUpdateLinkedRenewal",
            DeleteLinkedDebtRenewalEvent::class => "onDeleteLinkedRenewal",
        ];
    }

    public function onUpdateLinkedRenewal(UpdateLinkedDebtRenewalEvent $event)
    {
        $fund = $event->getEntity();
        $renewal = ($fund->getLinkedDebtRenewal())
            ->setAmount($fund->getCashOutflows())
            ->setWording($fund->getWording())
            ->setCreatedAt($fund->getCreatedAt())
            ->setObservation($fund->getObservations())
            ->setYear($fund->getYear());
        $renewal->getDebt()->setRenewalAt($renewal->getCreatedAt());
    }

    public function onCreate(CreateFundEvent $event)
    {
        $this->isDeleting = false;
        $this->handleCreatingOrDeleting($event->getEntity());
    }

    public function onUpdating(UpdateFundEvent $event)
    {
        $this->isDeleting = false;
        $fund = $event->getEntity();
        $previous = $event->getPrevious();
        if (
            $previous->getCreatedAt() !== $fund->getCreatedAt()
            || $previous->getCashBalances() !== $fund->getCashBalances()
        ) {
            $emptyFund = new class(
                $previous->getUser(),
                (int) $previous->getPreviousBalances(),
                (int) $previous->getPreviousTotalInflows(),
                (int) $previous->getPreviousTotalOutflows(),
                (int) $previous->getCashInFlows(),
                (int) $previous->getCashOutFlows(),
            ) extends Fund
            {
                public function __construct(
                    private User $user,
                    private int $previousBalances,
                    private int $previousTotalInflows,
                    private int $previousTotalOutflows,
                    private int $cashInFlows,
                    private int $cashOutFlows
                ) {
                }
            };

            if ($previous->getCreatedAt() < $fund->getCreatedAt()) {
                $anterior = $this->fundRepository
                    ->findWhereCreatedAt(
                        $previous->getCreatedAt(),
                        operator: "<",
                        conditions: ["user" => $fund->getUser()],
                        getOneResult: true
                    ) ?? $emptyFund;


                /** @var Fund[] $funds */
                $funds = $this->fundRepository
                    ->findWhereCreatedAt(
                        $previous->getCreatedAt(),
                        conditions: ["user" => $fund->getUser(), "id" => [$fund->getId(), "<>"]],
                        orderDesc: false
                    );
            } else {
                $anterior = $this->fundRepository
                    ->findWhereCreatedAt(
                        $fund->getCreatedAt(),
                        operator: "<",
                        conditions: ["user" => $fund->getUser()],
                        getOneResult: true
                    ) ?? $emptyFund;

                /** @var Fund[] $funds */
                $funds = $this->fundRepository
                    ->findWhereCreatedAt(
                        $fund->getCreatedAt(),
                        conditions: ["user" => $fund->getUser(), "id" => [$fund->getId(), "<>"]],
                        orderDesc: false
                    );
            }
            $this->updateNexts($fund, $anterior, $funds);
        }
    }

    public function onDelete(DeleteFundEvent $event)
    {
        $this->isDeleting = true;
        $fund = $event->getEntity();
        $this->handleCreatingOrDeleting($fund);
        $this->isDeleting = false;
    }

    private function updateNexts(Fund &$fund, Fund $anterior, array &$funds = [])
    {
        $fakeAccount = (new Account())
            ->setUser($fund->getUser())
            ->setCashBalances((int)$anterior->getPreviousBalances())
            ->resetCashInFlows((int)$anterior->getPreviousTotalInflows())
            ->resetCashOutFlows((int)$anterior->getPreviousTotalOutflows())
            ->setCashInFlows((int)$anterior->getCashInFlows())
            ->setCashOutFlows((int)$anterior->getCashOutFlows());

        if ($this->isDeleting === false) {
            array_unshift($funds, $fund);
        }

        $funds = $this->collection($funds)
            ->sortBy(fn (Fund $fund) => $fund->getCreatedAt());

        $after = null;
        /** @var Fund $after */
        foreach ($funds as $after) {
            $after
                ->setPreviousBalances((int)$fakeAccount->getCashBalances())
                ->setPreviousTotalInflows((int)$fakeAccount->getCashInflows())
                ->setPreviousTotalOutflows((int)$fakeAccount->getCashOutFlows())
                ->setCashBalances((int)$fakeAccount->getCashBalances());
            if ($after->isInflow()) {
                $after->setCashInFlows((int)$after->getCashInFlows());
            } else {
                $after->setCashOutFlows((int)$after->getCashOutFlows());
            }

            if ($after->isInflow()) {
                $fakeAccount->setCashInFlows(
                    (int)$after->getCashInFlows()
                );
            } else {

                $fakeAccount->setCashOutFlows(
                    (int)$after->getCashOutFlows()
                );
            }
        }
        if ($after) {
            $after->getAccount()
                ->resetCashOutFlows($fakeAccount->getCashOutFlows())
                ->resetCashInFlows($fakeAccount->getCashInFlows())
                ->setCashBalances($fakeAccount->getCashBalances());
            unset($fakeAccount);
        } else {
            if ($this->isDeleting) {
                $account = $fund->getAccount();
                if ($fund->isInflow()) {
                    $account->setCashInFlows(-$fund->getCashInFlows());
                } else {
                    $account->setCashOutFlows(-$fund->getCashOutFlows());
                }
            }
            $account->setCashBalances($fund->getPreviousBalances());
        }
    }

    private function handleCreatingOrDeleting(Fund $fund)
    {
        $anterior = $this->fundRepository
            ->findWhereCreatedAt(
                $fund->getCreatedAt(),
                operator: "<",
                conditions: ["user" => $fund->getUser()],
                getOneResult: true
            ) ?? new class(
                $fund->getUser(),
                0,
                0,
                0,
                0,
                0,
            ) extends Fund
            {
                public function __construct(
                    private User $user,
                    private int $previousBalances,
                    private int $previousTotalInflows,
                    private int $previousTotalOutflows,
                    private int $cashInFlows,
                    private int $cashOutFlows
                ) {
                }
            };


        /** @var Fund[] $funds */
        $funds = $this->fundRepository
            ->findWhereCreatedAt(
                $fund->getCreatedAt(),
                conditions: $this->isDeleting ? ["user" => $fund->getUser(), "id" => [$fund->getId(), "<>"]]
                    : ["user" => $fund->getUser()],
                orderDesc: false
            );
        $this->updateNexts($fund, $anterior, $funds);
    }
}
