<?php

namespace App\Entity\Main\Configs;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Main\Users\UserAction;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use App\Form\Extensions\Validations\UniqueEntity;
use App\Repository\Main\Configs\ModuleRepository;

/**
 * @ORM\Entity(repositoryClass=ModuleRepository::class)
 * @ORM\Table("module")
 * @UniqueEntity("name")
 */
class Module
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $name;

    /**
     * @ORM\Column(type="boolean", options={"default"=false})
     */
    private $isActivated = false;

    /**
     * @ORM\OneToMany(targetEntity=UserAction::class, mappedBy="module", orphanRemoval=true)
     */
    private $userActions;

    public function __construct()
    {
        $this->userActions = new ArrayCollection();
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

    public function getIsActivated(): ?bool
    {
        return $this->isActivated;
    }

    public function setIsActivated(bool $isActivated): self
    {
        $this->isActivated = $isActivated;

        foreach ($this->userActions as $action) {
            $action->setIsActivated($this->isActivated);
        }

        return $this;
    }

    /**
     * @return Collection|UserAction[]
     */
    public function getUserActions(): Collection
    {
        return $this->userActions;
    }

    public function addUserAction(UserAction $userAction): self
    {
        if (!$this->userActions->contains($userAction)) {
            $this->userActions[] = $userAction;
            $userAction->setModule($this);
        }

        return $this;
    }

    public function removeUserAction(UserAction $userAction): self
    {
        if ($this->userActions->removeElement($userAction)) {
            // set the owning side to null (unless already changed)
            if ($userAction->getModule() === $this) {
                $userAction->setModule(null);
            }
        }

        return $this;
    }
}
