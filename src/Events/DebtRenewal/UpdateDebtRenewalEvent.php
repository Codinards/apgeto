<?php

namespace App\Events\DebtRenewal;

use App\Entity\Main\Funds\DebtRenewal;
use App\Events\DebtRenewal\LinkedDebtRenewal;

class UpdateDebtRenewalEvent
{
    use LinkedDebtRenewal;

    /**
     * @param Debt $entity
     */
    public function __construct(private DebtRenewal $entity, private DebtRenewal $previous)
    {
    }

    public function getPrevious(): DebtRenewal
    {
        return $this->previous;
    }
}
