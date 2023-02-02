<?php

namespace App\Entity\Tontines;

use App\Entity\Tontines\Unity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class MultiWinnersSelection
{
    /**
     * @param Unity[]|Collection
     */
    private Collection|array $winners;

    public function __construct()
    {
        $this->winners = new ArrayCollection();
    }

    /**
     * @return Unity[]
     */
    public function getWinners(): array
    {
        return $this->winners->toArray();
    }

    /**
     * @param Unity[] $unities
     * @return self
     */
    public function setWinners(array $unities): self
    {

        foreach ($unities as $unity) {
            $this->winners->add(
                $unity //->resolveBenefitAt()
            );
        }
        return $this;
    }
}
