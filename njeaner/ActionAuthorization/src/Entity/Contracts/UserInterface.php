<?php

namespace Njeaner\ActionAuthorization\Entity\Contracts;

use Symfony\Component\Security\Core\User\UserInterface as SymfonyUserInterface;

interface UserInterface extends SymfonyUserInterface
{
    public function getRole(): ?RoleInterface;

    public function setRole(?RoleInterface $role): self;
}
