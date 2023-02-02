<?php

namespace App\Events\Fund;

use App\Entity\Main\Funds\Fund;

class CreateFundEvent
{
    public function __construct(
        private Fund $entity,
    ) {
    }

    public function getEntity(): Fund
    {
        return $this->entity;
    }
}
