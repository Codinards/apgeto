<?php

namespace App\Events\Debt;

use App\Entity\Main\Funds\Debt;

class CreateDebtEvent
{
    use LinkedDebt;

    public function __construct(private Debt $entity)
    {
    }
}
