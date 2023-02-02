<?php

namespace App\Events\Fund;

use App\Entity\Main\Funds\Fund;

class UpdateLinkedDebtRenewalEvent
{
    use FundLinked;

    public function __construct(
        private Fund $entity,
    ) {
    }
}
