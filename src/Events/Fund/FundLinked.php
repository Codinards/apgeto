<?php

namespace App\Events\Fund;

use App\Entity\Main\Funds\Fund;

trait FundLinked
{

    public function getEntity(): Fund
    {
        return $this->entity;
    }

    public function isOperationLinked(): bool
    {
        return $this->entity->getOperation() !== null;
    }

    public function isAssistanceLinked(): bool
    {
        return $this->entity->getAssistance() !== null;
    }
    public function isDebtRenewalLinked(): bool
    {
        return $this->entity->getLinkedDebtRenewal() !== null;
    }

    public function isLinked(): bool
    {
        return $this->isOperationLinked()
            || $this->isAssistanceLinked()
            || $this->isDebtRenewalLinked();
    }
}
