<?php

namespace Njeaner\ActionAuthorization\Entity\Contracts;

use Doctrine\Common\Collections\Collection;

interface RoleInterface
{
    /**
     * @return Collection|User[]
     */
    public function getUsers(): Collection;

    public function addUser(UserInterface $user): self;

    public function removeUser(UserInterface $user): self;

    /**
     * @return Collection|UserAction[]
     */
    public function getUserActions(): Collection;

    public function addUserAction(UserActionInterface $userAction): self;

    public function removeUserAction(UserActionInterface $userAction): self;

    public function hasUserAction(UserActionInterface $userAction): bool;
}
