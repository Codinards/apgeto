<?php

namespace App\Entity\Utils;

use App\Entity\Tontines\CotisationDay;
use App\Entity\Tontines\TontineurData;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;

class DataCotisation
{
    private Collection $unities;

    private string $name;

    private TontineurData $tontiner;

    public function __construct(?array $unities = null)
    {
        $data = new ArrayCollection();
        if ($unities) {
            foreach ($unities as $key => $unity) {
                if ($key === 0) {
                    $this->name = $unity->getTontine()->getType()->getName();
                }

                $data->add(
                    (new UnityCotisation($unity))->setName(($key > 0 ? ($key + 1 . ' ') : '') . "cotisation")
                );
            }
        }
        $this->unities = $data;
    }

    public function handleFailure(EntityManagerInterface $manager, CotisationDay $day): self
    {
        /** @var CotisationUnity $unity */
        foreach ($this->unities as $unity) {
            $unity->handleFailure($manager, $this->tontiner, $day);
        }

        return $this;
    }

    /**
     * @return Collection $unities
     */
    public function getUnities(): Collection
    {
        return $this->unities;
    }

    /**
     * @param Collection $unities
     *
     * @return  self
     */
    public function setUnities($unities): self
    {
        $this->unities = $unities;

        return $this;
    }

    public function addUnity(UnityCotisation $unity): self
    {
        $this->unities->add($unity);

        return $this;
    }


    /**
     * Get the value of name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the value of name
     *
     * @return  self
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Set the value of tontiner
     *
     * @return  self
     */
    public function setTontiner($tontiner)
    {
        $this->tontiner = $tontiner;

        return $this;
    }
}
