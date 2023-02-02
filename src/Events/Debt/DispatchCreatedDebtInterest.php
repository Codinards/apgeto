<?php

namespace App\Events\Debt;

use App\Entity\Main\Funds\Debt;
use App\Entity\Main\Funds\DebtInterest;

class DispatchCreatedDebtInterest
{
    public function __construct(
        /** @var Debt */
        private Debt $debt,
        /** @var DebtInterest */
        private DebtInterest $debtInterest
    ) {
    }

    /**
     * @return Debt
     */
    public function getDebt(): Debt
    {
        return $this->debt;
    }

    /**
     * @return DebtInterest
     */
    public function getDebtInterest(): DebtInterest
    {
        return $this->debtInterest;
    }
}
