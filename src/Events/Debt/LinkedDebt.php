<?php

namespace App\Events\Debt;

use App\Entity\Main\Funds\Debt;

trait LinkedDebt
{

    /**
     * @param Debt $entity
     */
    public function __construct(private Debt $entity)
    {
    }

    /**
     * @return Debt
     */
    public function getEntity(): Debt
    {
        return $this->entity;
    }

    public function isLinked(): bool
    {
        return $this->entity->getLinkedDebtRenewal() !== null;
    }
}
