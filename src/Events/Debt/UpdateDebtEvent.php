<?php

namespace App\Events\Debt;

use App\Entity\Main\Funds\Debt;

class UpdateDebtEvent
{
    use LinkedDebt;

    /**
     * @param Debt $entity
     */
    public function __construct(private Debt $entity, private Debt $previous)
    {
    }

    public function getPrevious(): Debt
    {
        return $this->previous;
    }
}
