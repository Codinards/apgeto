<?php

namespace App\Events\Assistance;

use App\Entity\Assistances\Contributor;
use App\Entity\Main\Funds\Fund;

class UpdateContributorFundEvent
{
    public function __construct(private Fund $entity)
    {
    }

    public function getEntity(): Fund
    {
        return $this->entity;
    }

    public function getContributor(): ?Contributor
    {
        return $this->getEntity()->getAssistance();
    }
}
