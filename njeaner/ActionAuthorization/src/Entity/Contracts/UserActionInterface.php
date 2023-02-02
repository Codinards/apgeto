<?php

namespace Njeaner\ActionAuthorization\Entity\Contracts;

use Doctrine\Common\Collections\Collection;

interface UserActionInterface
{
    public function getId(): ?int;

    public function getName(): ?string;

    public function setName(string $name): self;

    /**
     * @return Collection|RoleInterface[]
     */
    public function getRoles(): Collection;

    public function addRole(RoleInterface $role): self;

    public function removeRole(RoleInterface $role): self;

    public function hasRole(RoleInterface $role): bool;

    public function getTitle(): ?string;

    public function setTitle(string $title): self;

    public function getIsUpdatable(): ?bool;

    public function setIsUpdatable(bool $isUpdatable): self;

    public function getHasAuth(): ?bool;

    public function setHasAuth(bool $hasAuth): self;

    public function getIsIndex(): ?bool;

    public function setIsIndex(bool $isIndex): self;

    public function getIsActivated(): ?bool;

    public function setIsActivated(bool $isActivated): self;
}
