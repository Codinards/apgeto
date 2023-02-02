<?php

namespace App\Entity\Utils\SeveralFundsOperations;

use App\Entity\Main\Users\User;
use App\Entity\Utils\CashInFlows;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class MultiCashInFlows
{
    /**
     * @var CashInFlows[]|Collection
     */
    private Collection $targets;

    public function __construct(User $admin, private int $count = 2)
    {
        $this->targets = new ArrayCollection();
        for ($i = 0; $i < $count; $i++) {
            $this->targets->add((new CashInFlows())->setAdmin($admin));
        }
    }

    /**
     * Get the value of targets
     *
     * @return  CashInFlows[]|Collection
     */
    public function getTargets(): Collection
    {
        return $this->targets;
    }
}
