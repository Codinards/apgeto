<?php

namespace App\Entity\Tontines;

use App\Entity\Tontines\Unity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class MultiWinners
{
    /**
     * @param Unity[]
     */
    private array $winners = [];

    /**
     * @return Unity[]
     */
    public function getWinners(): array
    {
        return $this->winners;
    }

    /**
     * @param Unity[] $unities
     * @return self
     */
    public function setWinners(array $unities): self
    {

        foreach ($unities as $unity) {
            $this->winners[] = $unity->resolveBenefitAt();
        }
        return $this;
    }
}
