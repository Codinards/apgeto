<?php

namespace App\Entity\Utils\SeveralFundsOperations;

use App\Entity\Main\Users\User;
use App\Entity\Utils\CashOutFlows;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class MultiCashOutFlows
{
    /**
     * @var CashOutFlows[]|Collection
     */
    private Collection $targets;

    public function __construct(User $admin, private int $count = 3)
    {
        $this->targets = new ArrayCollection();
        for ($i = 0; $i < $count; $i++) {
            $this->targets->add((new CashOutFlows())->setAdmin($admin));
        }
    }

    /**
     * Get the value of targets
     *
     * @return  CashOutFlows[]|Collection
     */
    public function getTargets(): Collection
    {
        return $this->targets;
    }
}
