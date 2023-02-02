<?php

namespace App\Tools\Entity;

use App\Entity\Exceptions\AccountManagerException;
use App\Entity\Main\Funds\Account;
use App\Entity\Main\Users\User;
use App\Tools\AppConstants;

class FlowTransactionValidator
{

    public static function canLoan(/*User|Account*/$account, bool $throwException = true): bool
    {
        /** @var Account $account */
        $account = ($account instanceof User) ? $account->getAccount() : $account;
        if ($account->getLoanBalances() > 0 and !AppConstants::$USER_MULTIPLE_LOAN) {
            if (!$throwException) {

                return false;
            }
            throw (new AccountManagerException(AccountManagerException::USER_ALREADY_HAS_LOAN))->setParam('%balance%', $account->getLoanBalances());
        }

        if ($account->getCashBalances() < AppConstants::$LOAN_BASE_FUND) {
            if (!$throwException) {
                return false;
            }
            throw new AccountManagerException(AccountManagerException::USER_NO_HAS_LOAN_BASE_FUND);
        }
        return true;
    }
}
