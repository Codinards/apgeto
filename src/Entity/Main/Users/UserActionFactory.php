<?php

namespace App\Entity\Main\Users;

use Doctrine\Common\Collections\ArrayCollection;

class UserActionFactory
{
    private $roles;

    public function __construct()
    {
        $this->roles = new ArrayCollection();
    }

    /**
     * Get the value of roles
     */
    public function getRoles()
    {
        return $this->roles;
    }

    public function addRole(Role $role)
    {
        $this->roles->add($role);
    }
}
