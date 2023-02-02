<?php

namespace App\Events\Fund;

use App\Entity\Main\Funds\Fund;
use Doctrine\ORM\EntityManagerInterface;

class DeleteLinkedDebtRenewalEvent
{
    use FundLinked;

    public function __construct(
        private Fund $entity,
        private EntityManagerInterface $manager
    ) {
    }

    public function getManager(): EntityManagerInterface
    {
        return $this->manager;
    }
}
