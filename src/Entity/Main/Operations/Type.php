<?php

namespace App\Entity\Main\Operations;

use App\Entity\Main\Users\User;
use App\Repository\Main\Operations\TypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=TypeRepository::class)
 * @UniqueEntity("name")
 * @ORM\Table("operation_type")
 */
class Type
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
     * @ORM\Column(type="integer")
     */
    private $balance = 0;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="adminOperationTypes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $admin;

    /**
     * @ORM\OneToMany(targetEntity=Operation::class, mappedBy="type", orphanRemoval=true)
     */
    private $operations;

    /**
     * @var bool
     * @ORM\Column(type="boolean", nullable=true)
     */
    private bool $isUpdatable = true;


    public function __construct()
    {
        $this->operations = new ArrayCollection();
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

    public function getBalance(): ?int
    {
        return $this->balance;
    }

    public function setBalance(int $balance): self
    {
        $this->balance = $balance;

        return $this;
    }

    public function setInflow(int $inflow): self
    {
        $this->balance += $inflow;

        return $this;
    }

    public function setOutflow(int $outflow): self
    {
        $this->balance -= $outflow;

        return $this;
    }

    public function getAdmin(): ?User
    {
        return $this->admin;
    }

    public function setAdmin(?User $admin): self
    {
        $this->admin = $admin;

        return $this;
    }

    /**
     * @return Collection|Operation[]
     */
    public function getOperations(): Collection
    {
        return $this->operations;
    }

    public function addOperation(Operation $operation): self
    {
        if (!$this->operations->contains($operation)) {
            $this->operations[] = $operation;
            $operation->setType($this);
        }

        return $this;
    }

    public function removeOperation(Operation $operation): self
    {
        if ($this->operations->removeElement($operation)) {
            // set the owning side to null (unless already changed)
            if ($operation->getType() === $this) {
                $operation->setType(null);
            }
        }

        return $this;
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
}
