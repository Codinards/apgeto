<?php

namespace App\Events\Operation;

use App\Entity\Main\Funds\Fund;
use App\Entity\Main\Operations\Operation;

class DeleteOperationMemberFundEvent
{
    public function __construct(private Fund $entity)
    {
    }

    public function getEntity(): Fund
    {
        return $this->entity;
    }

    public function getOperation(): ?Operation
    {
        return $this->getEntity()->getOperation();
    }
}
