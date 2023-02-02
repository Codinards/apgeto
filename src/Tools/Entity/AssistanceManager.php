<?php

namespace App\Tools\Entity;

use App\Entity\Assistances\Assistance;
use App\Entity\Assistances\Contributor;
use App\Entity\Exceptions\AssistanceManagerException;
use App\Entity\Main\Funds\Fund;
use App\Entity\Main\Users\User;
use App\Entity\Utils\CashInFlows;
use App\Entity\Utils\CashOutFlows;
use App\Tools\AppConstants;
use App\Twig\TranslatorTwigExtension;
use DateTime;

class AssistanceManager
{
    protected $translatorExtension;

    public function __construct(TranslatorTwigExtension $translatorExtension)
    {
        $this->translatorExtension = $translatorExtension;
    }

    public function manageCreateAssistance(Assistance $assistance, User $admin, AccountManager $accountManager): Assistance
    {
        $contributors = $assistance->getContributors();
        /** @var Contributor $contributor */
        foreach ($contributors as $contributor) {
            $fund = $accountManager->ManageCashOutFlows(
                (new CashOutFlows())
                    ->setAccount($contributor->getAccount())
                    ->setUser($contributor->getUser())
                    ->setAdmin($admin)
                    ->setCashOutFlows($contributor->getAmount())
                    ->setCreatedAt($assistance->getCreatedAt())
                    ->setWording($this->translatorExtension->__u('cancelling') . ' | ' . $assistance->getWording()),
                false
            );
            if ($fund->getCashBalances() < 0 and AppConstants::$FUND_CAN_BE_NEGATIVE === false) {
                throw (new AssistanceManagerException('assistance.out.flows.more.than.balance'))
                    ->setParams([
                        '%withdrawal%' => $contributor->getAmount(),
                        '%balance%' => $contributor->getAccount()->getCashBalances(),
                        '%user%' => $contributor->getUser(true)->getName()
                    ]);
            }
            $contributor->setFund($fund);
            $contributor->getAccount()->setCashOutFlows($contributor->getAmount());
        }
        if ($assistance->getType()->getAmountType() !== 3) {
            $assistance->setAmount($assistance->getContributors()->first()->getAmount());
        }

        return $assistance;
    }

    // public function manageDelete(Assistance $assistance, int|User $admin, AccountManager $accountManager): Assistance
    // {
    //     /** @var Contributor $contributor */
    //     foreach ($assistance->getContributors() as $contributor) {

    //         $fund = $accountManager->ManageCashInFlows(
    //             (new CashInFlows())
    //                 ->setAccount($contributor->getAccount())
    //                 ->setUser($contributor->getUser())
    //                 ->setAdmin($admin)
    //                 ->setCashInFlows($contributor->getAmount())
    //                 ->setCreatedAt($assistance->getCreatedAt())
    //                 ->setWording($this->translatorExtension->__u('cancelling') . ' | ' . $assistance->getWording())
    //         );
    //         $contributor->getAccount()->setCashInFlows($contributor->getAmount());
    //         $contributor->setFund($fund);
    //     }

    //     return $assistance;
    // }
}
