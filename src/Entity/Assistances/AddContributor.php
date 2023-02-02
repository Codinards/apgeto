<?php

namespace App\Entity\Assistances;

use App\Entity\Main\Users\User;

class AddContributor
{
    private bool $isSelected = false;

    public function __construct(private User $user)
    {
    }

    public function getIsSelected(): bool
    {
        return $this->isSelected;
    }

    public function setIsSelected(bool $isSelected): self
    {
        $this->isSelected = $isSelected;

        return $this;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @return  self
     */
    public function setUser(User $user)
    {
        $this->user = $user;

        return $this;
    }
}
