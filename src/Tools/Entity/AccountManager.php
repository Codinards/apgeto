<?php

namespace App\Tools\Entity;

use App\Entity\Exceptions\AccountManagerException;
use App\Entity\Main\Funds\Account;
use App\Entity\Main\Funds\Debt;
use App\Entity\Main\Funds\DebtAvalist;
use App\Entity\Main\Funds\Fund;
use App\Entity\Utils\CashInFlows;
use App\Entity\Utils\CashOutFlows;
use App\Entity\Utils\LoanDataUpdate;
use App\Entity\Utils\LoanInFlows;
use App\Entity\Utils\LoanOutFlows;
use App\Tools\AppConstants;
use App\Twig\DateFormatterExtension;
use App\Twig\NumberTwigExtension;
use Doctrine\Persistence\ObjectManager;

class AccountManager
{
    protected $numberTwigExtension;

    public function __construct(NumberTwigExtension $numberTwigExtension)
    {
        $this->numberTwigExtension = $numberTwigExtension;
    }

    public function ManageCashInFlows(CashInFlows $cashInFlows): Fund
    {
        $account = $cashInFlows->getAccount();
        $fund = (new Fund())
            ->setCashBalances($cashInFlows->getAccount()->getCashBalances())
            ->setPreviousBalances($account->getCashBalances())
            ->setPreviousTotalInflows($account->getCashInFlows())
            ->setPreviousTotalOutflows($account->getCashOutFlows())
            ->setWording($cashInFlows->getWording())
            ->setCashInFlows($cashInFlows->getCashInFlows())
            ->setObservations($cashInFlows->getObservations())
            ->setAccount($cashInFlows->getAccount())
            ->setUser($cashInFlows->getUser())
            ->setCreatedAt($cashInFlows->getCreatedAt())
            ->setAdmin($cashInFlows->getAdmin())
            ->setYear($cashInFlows->getYear())
            ->setType(Account::INFLOW);
        $fund->getAccount()->setCashInFlows($fund->getCashInFlows());

        return $fund;
    }

    public function ManageCashOutFlows(CashOutFlows $cashOutFlows, bool $throwException = true): Fund
    {
        $account = $cashOutFlows->getAccount();
        $balance = $account->getCashBalances() - $cashOutFlows->getCashOutFlows();
        if ($throwException) {
            if ($balance < 0 and !AppConstants::$FUND_CAN_BE_NEGATIVE) {
                throw new AccountManagerException(AccountManagerException::NO_NEGATIVE_FUND);
            }
            if (AppConstants::$FUND_CAN_BE_NEGATIVE == false && ($balance < $baseFund = AppConstants::$ACCOUNT_BASE_FUND)) {
                throw (new AccountManagerException(AccountManagerException::FUND_NO_LESS_THAN))
                    ->setParams([
                        '%withdrawal%' => $this->formatMoney($cashOutFlows->getCashOutFlows()),
                        '%balance%' => $this->formatMoney($account->getCashBalances())
                    ]);
            }
        }
        $fund = (new Fund())
            ->setCashBalances($account->getCashBalances())
            ->setPreviousBalances($account->getCashBalances())
            ->setPreviousTotalInflows($account->getCashInFlows())
            ->setPreviousTotalOutflows($account->getCashOutFlows())
            ->setWording($cashOutFlows->getWording())
            ->setCashOutFlows($cashOutFlows->getCashOutFlows())
            ->setObservations($cashOutFlows->getObservations())
            ->setAccount($cashOutFlows->getAccount())
            ->setUser($cashOutFlows->getUser())
            ->setCreatedAt($cashOutFlows->getCreatedAt())
            ->setAdmin($cashOutFlows->getAdmin())
            ->setYear($cashOutFlows->getYear())
            ->setType(Account::OUTFLOW);
        $fund->getAccount()->setCashOutFlows($fund->getCashOutFlows());

        return $fund;
    }

    public function ManageLoanInFlows(LoanInFlows $loanInFlows, bool $method_post = false): Debt
    {
        $loanInFlows->resolveInterest();
        $account = $loanInFlows->getAccount();
        $debt = (new Debt())
            ->setLoanBalances($account->getLoanBalances());
        if ($account->canLoan()) {
            $debt
                ->setWording($loanInFlows->getWording())
                ->setLoanInFlows($loanInFlows->getloanInFlows())
                ->setPreviousBalances($account->getLoanBalances())
                ->setPreviousTotalInflows($account->getLoanInFlows())
                ->setPreviousTotalOutflows($account->getLoanOutFlows())
                ->setAccount($loanInFlows->getAccount())
                ->setUser($loanInFlows->getUser())
                ->setCreatedAt($loanInFlows->getCreatedAt())
                ->setObservations($loanInFlows->getObservations())
                ->setAdmin($loanInFlows->getAdmin())
                ->setInterests($loanInFlows->getInterests())
                ->setPaybackAt(
                    (new \DateTime($loanInFlows->getCreatedAt()->format("Y-m-d")))
                        ->add($loanInFlows->getRenewalPeriod())
                )
                ->setRenewalPeriod($loanInFlows->getRenewalPeriod())
                ->setType(Account::INFLOW);
            if ($method_post and ($debt->getPaybackAt() <= $debt->getCreatedAt())) {
                throw (new AccountManagerException(AccountManagerException::PAYBACKAT_GREAT_THAN_CREATEDAT))
                    ->setParams([
                        '%created_at%' => DateFormatterExtension::getInstance()->dateInLocale($debt->getCreatedAt(), 'll'),
                        '%payback_at%' => DateFormatterExtension::getInstance()->dateInLocale($debt->getPaybackAt(), 'll')
                    ]);
            }
            foreach ($loanInFlows->getAvalistes() as $avaliste) {
                $debt->addAvaliste($avaliste);
            }

            $account->setLoanInFlows($loanInFlows->getLoanInFlows());

            return $debt;
        }
    }

    public function ManageLoanOutFlows(LoanOutFlows $loanOutFlows, ObjectManager $manager): Debt
    {
        $account = $loanOutFlows->getAccount();
        if ($account->hasLoan()) {
            $balance = $account->getLoanBalances() - $loanOutFlows->getLoanOutFlows();
            if ($balance < 0) {
                throw (new AccountManagerException(AccountManagerException::LOAN_OUT_FLOWS_MORE_THAN_BALANCE))
                    ->setParams([
                        '%withdrawal%' => $this->formatMoney($loanOutFlows->getLoanOutFlows()),
                        '%balance%' => $this->formatMoney($account->getCashBalances())
                    ]);
            }
            $currentDebt = $account->getCurrentDebt();
            $debt = (new Debt())
                ->setLoanBalances($loanOutFlows->getAccount()->getLoanBalances())
                ->setWording($loanOutFlows->getWording())
                ->setLoanOutFlows($loanOutFlows->getLoanOutFlows())
                ->setPreviousBalances($account->getLoanBalances())
                ->setPreviousTotalInflows($account->getLoanInFlows())
                ->setPreviousTotalOutflows($account->getLoanOutFlows())
                ->setObservations($loanOutFlows->getObservations())
                ->setAccount($loanOutFlows->getAccount())
                ->setUser($loanOutFlows->getUser())
                ->setCreatedAt($loanOutFlows->getCreatedAt())
                ->setAdmin($loanOutFlows->getAdmin())
                ->setYear($loanOutFlows->getYear())
                ->setParent($currentDebt)
                ->setType(Account::OUTFLOW);
            $debt->getAccount()->setLoanOutFlows($debt->getLoanOutFlows());

            if ($account->getLoanBalances() === 0) {


                /** @var Debt[] */
                //$children = $manager->getRepository(Debt::class)->findBy(['account' => $account, 'parent' => $currentDebt]);
                $children = $currentDebt->getChildren();
                $currentDebt->setIsCurrent(false);
                $account->reinitializeDebt();
                foreach ($children as $child) {
                    $child->setIsCurrent(false);
                }
                $debt->setIsCurrent(false);
            }

            return $debt;
        }
        throw new AccountManagerException(AccountManagerException::NO_EXISTING_DEBT);
    }

    public function manageLoanUpdate(LoanDataUpdate $loanUpdated, Debt $debt): Debt
    {
        $account = $loanUpdated->getAccount();
        if ($account->hasLoan()) {
            $debt
                ->setLoanBalances($loanUpdated->getLoanBalances() - $debt->getLoanInFlows())
                ->setWording($loanUpdated->getWording())
                ->setObservations($loanUpdated->getObservations())
                ->setCreatedAt($loanUpdated->getCreatedAt());
            if ($loanUpdated->getIsInflow()) {
                if (!$debt->isLinked() and ($debt->getPaybackAt() <= $debt->getCreatedAt())) {
                    throw (new AccountManagerException(AccountManagerException::PAYBACKAT_GREAT_THAN_CREATEDAT))
                        ->setParams([
                            '%created_at%' => DateFormatterExtension::getInstance()->dateInLocale($debt->getCreatedAt(), 'll'),
                            '%payback_at%' => DateFormatterExtension::getInstance()->dateInLocale($debt->getPaybackAt(), 'll')
                        ]);
                }

                $debt
                    ->setLoanInFlows($loanUpdated->getLoanInFlows())
                    ->setInterests($loanUpdated->getInterests())
                    ->setPaybackAt(
                        (new \DateTime($loanUpdated->getCreatedAt()->format("Y-m-d")))
                            ->add($loanUpdated->getRenewalPeriod())
                    )
                    ->setRenewalPeriod($loanUpdated->getRenewalPeriod());
                /** @var DebtAvalist $avaliste */
                foreach ($loanUpdated->getAvalistes() as $avaliste) {
                    $debt->addAvaliste($avaliste);
                    if ($avaliste->getDebt() == null) {
                        $avaliste->setDebt($debt);
                    }
                }
                $debt->getAccount()->resetLoanInFlows($debt->getLoanInFlows());
            } else {
                $debt->setLoanOutFlows($loanUpdated->getLoanOutFlows());
                $debt->getAccount()->resetLoanOutFlows($debt->getLoanOutFlows());
            }


            return $debt;
        }
        throw new AccountManagerException(AccountManagerException::NO_EXISTING_DEBT);
    }

    public function formatMoney(int $amount): string
    {
        return $this->numberTwigExtension->moneyFormat($amount);
    }
}
