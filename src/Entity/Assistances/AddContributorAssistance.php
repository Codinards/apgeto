<?php

namespace App\Entity\Assistances;

use App\Entity\Main\Users\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class AddContributorAssistance
{
    /**
     * @var AddContributor[]|Collection
     */
    private Collection $contributors;

    public function __construct()
    {
        $this->contributors = new ArrayCollection();
    }


    /**
     * @return AddContributor[]|Collection
     */
    public function getContributors(): Collection
    {
        return $this->contributors;
    }

    /**
     * @param User[] $contributors
     * @return  self
     */
    public function setContributors(array $contributors)
    {
        foreach ($contributors as $contributor) {
            $contributor = new AddContributor($contributor);
            $this->contributors->add($contributor);
        }

        return $this;
    }
}
