<?php

namespace App\Events\DebtRenewal;

use App\Entity\Main\Funds\DebtRenewal;

trait LinkedDebtRenewal
{

    /**
     * @param DebtRenewal $entity
     */
    public function __construct(private DebtRenewal $entity)
    {
    }

    /**
     * @return DebtRenewal
     */
    public function getEntity(): DebtRenewal
    {
        return $this->entity;
    }

    public function isFundLinked(): bool
    {
        return $this->entity->getLinkedFund() !== null;
    }

    public function isDebtLinked(): bool
    {
        return $this->entity->getLinkedDebt() !== null;
    }

    public function isLinked(): bool
    {
        return $this->isFundLinked() || $this->isDebtLinked();
    }
}
