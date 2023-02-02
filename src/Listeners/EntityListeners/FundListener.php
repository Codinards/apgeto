<?php

namespace App\Listeners\EntityListeners;

use App\Entity\Main\Funds\Account;
use App\Entity\Main\Funds\Fund;
use App\Entity\Main\Users\User;
use App\Repository\Main\Funds\DebtRenewalRepository;
use App\Repository\Main\Funds\FundRepository;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;

class FundListener
{

    private static bool $isUpdating = true;

    public function __construct(
        private FundRepository $fundRepository,
        private DebtRenewalRepository $debtRenewalRepository
    ) {
    }

    public function postLoad(LifecycleEventArgs $event)
    {
        if ($fund = $this->isFund($event)) {
            if ($renewal = $this->debtRenewalRepository
                ->findOneBy(["linkedFund" => $fund])
            ) {

                $fund->setLinkedDebtRenewal($renewal);
            }
        }
    }

    public function prePersist(LifecycleEventArgs $event)
    {
        if ($fund = $this->isFund($event)) {
            $anterior = $this->baseController->getFundRepository()
                ->findWhereCreatedAt(
                    $fund->getCreatedAt(),
                    operator: "<",
                    conditions: ["user" => $fund->getUser()],
                    getOneResult: true
                ) ?? $fund;


            /** @var Fund[] $funds */
            $funds = $this->baseController->getFundRepository()
                ->findWhereCreatedAt(
                    $fund->getCreatedAt(),
                    conditions: ["user" => $fund->getUser()],
                    orderDesc: false
                );
            if (!empty($funds)) {
                array_unshift($funds, $fund);

                $funds = $this->baseController->collection($funds)
                    ->sortBy(fn (Fund $fund) => $fund->getCreatedAt());
                $fakeAccount = (new Account())
                    ->setUser($fund->getUser())
                    ->setCashBalances((int)$anterior->getPreviousBalances())
                    ->resetCashInFlows((int)$anterior->getPreviousTotalInflows())
                    ->resetCashOutFlows((int)$anterior->getPreviousTotalOutflows())
                    ->setCashInFlows((int)$anterior->getCashInFlows())
                    ->setCashOutFlows((int)$anterior->getCashOutFlows());

                /** @var Fund $after */
                foreach ($funds as $after) {
                    $after
                        ->setPreviousBalances((int)$fakeAccount->getCashBalances())
                        ->setPreviousTotalInflows((int)$fakeAccount->getCashInflows())
                        ->setPreviousTotalOutflows((int)$fakeAccount->getCashOutFlows())
                        ->setCashBalances((int)$fakeAccount->getCashBalances())
                        ->setCashInFlows((int)$after->getCashInFlows())
                        ->setCashOutFlows((int)$after->getCashOutFlows());
                    $fakeAccount->setCashInFlows(
                        $after->getCashInflows()
                    )
                        ->setCashOutFlows(
                            $after->getCashOutFlows()
                        );
                }
                $after->getAccount()
                    ->resetCashOutFlows($fakeAccount->getCashOutFlows())
                    ->resetCashInFlows($fakeAccount->getCashInFlows())
                    ->setCashBalances($fakeAccount->getCashBalances());
                unset($fakeAccount);
            }
        }
    }


    public function preUpdate(PreUpdateEventArgs $event)
    {
        // if ($fund = $this->isFund($event) and self::$isUpdating) {
        //     if (!$event->hasChangedField("createdAt") && !$event->hasChangedField("cashBalances")) {
        //         return;
        //     }

        //     if (
        //         $event->hasChangedField("createdAt")
        //         && (($oldCreatedAt = $event->getOldValue("createdAt")) < $fund->getCreatedAt())
        //     ) {
        //         $anterior = $this->baseController->getFundRepository()
        //             ->findWhereCreatedAt(
        //                 $oldCreatedAt,
        //                 operator: "<",
        //                 conditions: ["user" => $fund->getUser()],
        //                 getOneResult: true
        //             ) ?? new class(
        //                 $event->getOldValue('user'),
        //                 $event->getOldValue("previousBalances"),
        //                 $event->getOldValue("previousTotalInflows"),
        //                 $event->getOldValue("previousTotalOutflows"),
        //                 $event->getOldValue("cashInFlows"),
        //                 $event->getOldValue("cashOutFlows"),
        //             ) extends Fund
        //             {
        //                 public function __construct(
        //                     private User $user,
        //                     private int $previousBalances,
        //                     private int $previousTotalInflows,
        //                     private int $previousTotalOutflows,
        //                     private int $cashInFlows,
        //                     private int $cashOutFlows
        //                 ) {
        //                 }
        //             };


        //         /** @var Fund[] $funds */
        //         $funds = $this->baseController->getFundRepository()
        //             ->findWhereCreatedAt(
        //                 $oldCreatedAt,
        //                 conditions: ["user" => $fund->getUser(), "id" => [$fund->getId(), "<>"]],
        //                 orderDesc: false
        //             );
        //     } else {
        //         $anterior = $this->baseController->getFundRepository()
        //             ->findWhereCreatedAt(
        //                 $fund->getCreatedAt(),
        //                 operator: "<",
        //                 conditions: ["user" => $fund->getUser()],
        //                 getOneResult: true
        //             ) ?? $fund;

        //         /** @var Fund[] $funds */
        //         $funds = $this->baseController->getFundRepository()
        //             ->findWhereCreatedAt(
        //                 $fund->getCreatedAt(),
        //                 conditions: ["user" => $fund->getUser(), "id" => [$fund->getId(), "<>"]],
        //                 orderDesc: false
        //             );
        //     }
        //     if (!empty($funds)) {
        //         array_unshift($funds, $fund);
        //         $funds = $this->baseController->collection($funds)
        //             ->sortBy(fn (Fund $fund) => $fund->getCreatedAt());

        //         $fakeAccount = (new Account())
        //             ->setUser($fund->getUser())
        //             ->setCashBalances((int)$anterior->getPreviousBalances())
        //             ->resetCashInFlows((int)$anterior->getPreviousTotalInflows())
        //             ->resetCashOutFlows((int)$anterior->getPreviousTotalOutflows())
        //             ->setCashInFlows((int)$anterior->getCashInFlows())
        //             ->setCashOutFlows((int)$anterior->getCashOutFlows());

        //         /** @var Fund $after */
        //         foreach ($funds as $after) {
        //             $after
        //                 ->setPreviousBalances((int)$fakeAccount->getCashBalances())
        //                 ->setPreviousTotalInflows((int)$fakeAccount->getCashInflows())
        //                 ->setPreviousTotalOutflows((int)$fakeAccount->getCashOutFlows())
        //                 ->setCashBalances((int)$fakeAccount->getCashBalances())
        //                 ->setCashInFlows((int)$after->getCashInFlows())
        //                 ->setCashOutFlows((int)$after->getCashOutFlows());
        //             $fakeAccount->setCashInFlows(
        //                 $after->getCashInflows()
        //             )
        //                 ->setCashOutFlows(
        //                     $after->getCashOutFlows()
        //                 );
        //             $this->baseController->getManager()->persist($after);
        //         }
        //         $after->getAccount()
        //             ->resetCashOutFlows($fakeAccount->getCashOutFlows())
        //             ->resetCashInFlows($fakeAccount->getCashInFlows())
        //             ->setCashBalances($fakeAccount->getCashBalances());
        //         unset($fakeAccount);
        //         self::$isUpdating = false;
        //     }
        // }
    }

    public function preDelete(LifecycleEventArgs $event)
    {
        $this->prePersist($event);
    }

    private function isFund(LifecycleEventArgs $event): ?Fund
    {
        return ($fund = $event->getObject()) instanceof Fund ? $fund : null;
    }
}
