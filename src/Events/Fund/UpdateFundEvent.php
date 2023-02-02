<?php

namespace App\Events\Fund;

use App\Entity\Main\Funds\Fund;

class UpdateFundEvent
{
    use FundLinked;

    public function __construct(
        private Fund $entity,
        private fund $previous
    ) {
    }



    public function getPrevious(): Fund
    {
        return $this->previous;
    }
}
