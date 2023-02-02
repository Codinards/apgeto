<?php

namespace App\Entity\Main\Users;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Main\Configs\Module;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use App\Form\Extensions\Validations\UniqueEntity;
use App\Repository\Main\Users\UserActionRepository;

/**
 * @ORM\Entity(repositoryClass=UserActionRepository::class)
 * @ORM\Table("action")
 * @UniqueEntity("name")
 * @UniqueEntity("title")
 */
class UserAction
{
    const ACTION_NAME = 'action_name';
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * 
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * 
     */
    private $name;

    /**
     * @ORM\ManyToMany(targetEntity=Role::class, mappedBy="userActions")
     */
    private $roles;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * 
     */
    private $title;

    /**
     * @ORM\Column(type="boolean", options={"default":true})
     * 
     */
    private $isUpdatable = true;



    /**
     * @ORM\Column(type="boolean", options={"default":true})
     * 
     */
    private $hasAuth = true;

    /**
     * @ORM\Column(type="boolean", options={"default":false})
     * 
     */
    private $isIndex = false;

    /**
     * @ORM\ManyToOne(targetEntity=Module::class, inversedBy="userActions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $module;

    /**
     * @ORM\Column(type="boolean")
     * 
     */
    private $isActivated;

    public function __construct()
    {
        $this->roles = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection|Role[]
     */
    public function getRoles(): Collection
    {
        return $this->roles;
    }

    public function addRole(Role $role): self
    {
        if (!$this->roles->contains($role)) {
            $this->roles[] = $role;
        }

        return $this;
    }

    public function removeRole(Role $role): self
    {
        $this->roles->removeElement($role);

        return $this;
    }

    public function hasRole(Role $role): bool
    {
        return $this->roles->contains($role);
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function __toString()
    {
        return $this->title;
    }

    public function getIsUpdatable(): ?bool
    {
        return $this->isUpdatable;
    }

    public function setIsUpdatable(bool $isUpdatable): self
    {
        $this->isUpdatable = $isUpdatable;

        return $this;
    }

    public function getHasAuth(): ?bool
    {
        return $this->hasAuth;
    }

    public function setHasAuth(bool $hasAuth): self
    {
        $this->hasAuth = $hasAuth;

        return $this;
    }

    public function getIsIndex(): ?bool
    {
        return $this->isIndex;
    }

    public function setIsIndex(bool $isIndex): self
    {
        $this->isIndex = $isIndex;

        return $this;
    }

    public function getModule(): ?Module
    {
        return $this->module;
    }

    public function setModule(?Module $module): self
    {
        $this->module = $module;
        $this->isActivated = $module->getIsActivated();

        return $this;
    }

    public function getIsActivated(): ?bool
    {
        return $this->isActivated;
    }

    public function setIsActivated(bool $isActivated): self
    {
        $this->isActivated = $isActivated;

        return $this;
    }
}
