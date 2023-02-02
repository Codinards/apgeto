<?php

namespace App\Events\Fund;

use App\Entity\Main\Funds\Fund;

class DeleteFundEvent
{
    use FundLinked;

    public function __construct(
        private Fund $entity
    ) {
    }
}
