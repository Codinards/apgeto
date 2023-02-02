<?php

namespace App\Entity\Utils;

use App\Entity\Tontines\CotisationDay;
use App\Entity\Tontines\CotisationFailure;
use App\Entity\Tontines\TontineurData;
use App\Entity\Tontines\Unity;
use Doctrine\ORM\EntityManagerInterface;

class UnityCotisation
{
    private string $name;

    private bool $isChecked = false;

    // private Unity $unity;

    public function __construct(private ?Unity $unity = null)
    {
    }

    public function handleFailure(EntityManagerInterface $manager, TontineurData $tontiner, CotisationDay $day): self
    {
        if ((bool) $this->isChecked === true) {
            $manager->persist(
                (new CotisationFailure())
                    ->setTontiner($tontiner)
                    ->setUnity($this->unity)
                    ->setCotisationDay($day)
                    ->setTontine($tontiner->getTontine())
            );
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * 
     * @return  self
     */
    public function setName($name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return  bool
     */
    public function getIsChecked(): bool
    {
        return $this->isChecked;
    }

    /**
     * @param bool $isChecked
     *
     * @return  self
     */
    public function setIsChecked($isChecked): self
    {
        $this->isChecked = $isChecked;

        return $this;
    }
}
