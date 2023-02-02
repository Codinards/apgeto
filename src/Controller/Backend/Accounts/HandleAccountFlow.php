<?php

namespace App\Controller\Backend\Accounts;

use App\Entity\Main\Funds\Debt;
use App\Entity\Main\Funds\Fund;
use App\Repository\Main\Funds\DebtRepository;
use App\Repository\Main\Funds\FundRepository;
use App\Twig\DateFormatterExtension;

trait HandleAccountFlow
{

    private function updateBeforeCreated(Fund|Debt $entity, array $nexts = [], null|Fund|Debt $previous = null)
    {
        if ($this->isLastCreated($entity) === false) {
            /** @var null|Fund|Debt $previous */
            $previous = $previous ?? $this->getFundRepository()
                ->findWhereCreatedAt(
                    $entity->getCreatedAt(),
                    operator: "<",
                    conditions: ["user" => $entity->getUser()],
                    getOneResult: true
                );

            if (null == $previous) $this->throwRedirectRequest(
                true,
                $this->generateUrl('app_backend_fund_show', ['id' => $entity->getAccount()->getId()]),
                message: "Désolé, Cette action n'est pas possible car il n'y a pas d'opération antérieur à la date du "
                    . DateFormatterExtension::getInstance()->dateInLocale($entity->getCreatedAt()),
                is_path: true
            );

            /** @var Fund[]|Debt $nexts */
            $nexts = !empty($nexts) ? $nexts : $this->getFundRepository()
                ->findWhereCreatedAt(
                    $entity->getCreatedAt(),
                    conditions: ["user" => $entity->getUser(), "id" => [$entity->getId(), "<>"]],
                    orderDesc: false
                );
            return $this->handleUpdate($entity, $nexts, $previous);
        }

        if ($previous) {
            return $this->handleUpdate($entity, $nexts, $previous);
        }
        return [];
    }

    public function isLastCreated(Fund | Debt $entity): bool
    {
        $method = $entity instanceof Fund ? "getFundRepository" : "getDebtRepository";
        /** @var DebtRepository|FundRepository $repo */
        $repo = $this->$method();

        /** @var null|Debt|Fund $next */
        $next = $repo
            ->findWhereCreatedAt(
                $entity->getCreatedAt(),
                operator: ">",
                conditions: ["user" => $entity->getUser()],
                getOneResult: true
            );
        return $next ? false : true;
    }


    private function handleUpdate(Fund|Debt &$new, array $nexts, Fund|Debt &$last)
    {
        $isAnFundIstance = $new instanceof Fund;
        /** Definitions des donnees et methodes a utiliser */
        $lastBalanceGetMethod = $isAnFundIstance ? "getCashBalances" : "getLoanBalances";
        $setBalanceMethod = $isAnFundIstance ? "setCashBalances" : "setLoanBalances";
        $setbalanceOutflowMethod = $isAnFundIstance ? "setCashOutflows" : "setLoanOutflows";
        $setbalanceInflowMethod = $isAnFundIstance ? "setCashInflows" : "setLoanInflows";
        $getBalanceInflowsMethod = $isAnFundIstance ? "getCashInflows" : "getLoanInflows";
        $getBalanceOutflowsMethod = $isAnFundIstance ? "getCashOutflows" : "getLoanOutflows";
        /** Recuperation des donnees de l'entité enterieure */
        $previousCashBalance = $last->$lastBalanceGetMethod();
        $previousInflows = $last->getPreviousTotalInflows();
        $previousOutflow = $last->getPreviousTotalOutflows();

        /** mise a jour des donnees de l'entité a modifier */
        // $new
        //     ->setPreviousBalances($previousCashBalance)
        //     ->$setBalanceMethod($previousCashBalance)
        //     ->$getBalanceInflowsMethod($new->getCashInFlows())
        //     ->$setbalanceOutflowMethod($new->getCashOutFlows())
        //     ->setPreviousTotalInflows($previousInflows)
        //     ->setPreviousTotalOutflows($previousOutflow);

        $new->setPreviousBalances($previousCashBalance)
            ->$setBalanceMethod($previousCashBalance)
            ->$setbalanceInflowMethod($new->$getBalanceInflowsMethod())
            ->$setbalanceOutflowMethod($new->$getBalanceOutflowsMethod())
            ->setPreviousTotalInflows($previousInflows)
            ->setPreviousTotalOutflows($previousOutflow);

        array_unshift($nexts, $new);

        /** mise a jour des donnees des entités posterieures */
        /** @var Fund[]|Debt[] $nexts */
        foreach ($nexts as $key => $after) {

            if ($key !== 0) {
                $previous = $nexts[$key - 1];
                $after
                    ->setPreviousBalances($previous->$lastBalanceGetMethod())
                    ->setPreviousTotalInflows($previous->getPreviousTotalInflows() + $after->getCashInFlows())
                    ->setPreviousTotalOutflows($previous->getPreviousTotalOutflows() + $after->getCashOutFlows())
                    ->$setBalanceMethod($previous->$lastBalanceGetMethod())
                    ->$setbalanceInflowMethod($after->$getBalanceInflowsMethod())
                    ->$setbalanceOutflowMethod($after->$getBalanceOutflowsMethod());
            }
        }
        dd($new);
        if ($new instanceof Fund) {
            $new->getAccount()
                ->setCashBalances($after->getCashBalances())
                ->resetCashInFlows($after->getPreviousTotalInflows())
                ->resetCashOutFlows($after->getPreviousTotalOutflows());
            dd($new->getAccount());
        } else {
            $new->getAccount()
                ->setLoanOutFlows($after->getPreviousTotalOutflows())
                ->setLoanBalances($after->getCashBalances());
        }
        dd($new);
        return $nexts;
    }
}
