<?php

namespace App\Entity\Assistances;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use App\Form\Extensions\Validations\UniqueEntity;
use App\Repository\Assistances\AssistanceTypeRepository;

/**
 * @ORM\Entity(repositoryClass=AssistanceTypeRepository::class)
 * @ORM\Table("assistance_type")
 * @UniqueEntity("name")
 */
class AssistanceType
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
     * @ORM\Column(type="integer", nullable=true)
     */
    private $amount;

    /**
     * @ORM\Column(name="is_amount", type="boolean", nullable=true)
     */
    private $isAmount;

    /**
     * @ORM\OneToMany(targetEntity=Assistance::class, mappedBy="type", orphanRemoval=true)
     */
    private $assistances;

    public function __construct()
    {
        $this->assistances = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->name;
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

    public function getAmount(): ?int
    {
        return $this->amount;
    }

    public function setAmount(?int $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getIsAmount(): null|int|bool
    {
        return $this->isAmount ?? 2;
    }

    public function getAmountType(): int
    {
        return $this->amount === null ? 3 : ($this->isAmount == true ? 1 : 2);
    }

    public function setIsAmount(?bool $isAmount): self
    {
        $this->isAmount = $isAmount;

        return $this;
    }

    /**
     * @return Collection|Assistance[]
     */
    public function getAssistances(): Collection
    {
        return $this->assistances;
    }

    public function addAssistance(Assistance $assistance): self
    {
        if (!$this->assistances->contains($assistance)) {
            $this->assistances[] = $assistance;
            $assistance->setType($this);
        }

        return $this;
    }

    public function removeAssistance(Assistance $assistance): self
    {
        if ($this->assistances->removeElement($assistance)) {
            // set the owning side to null (unless already changed)
            if ($assistance->getType() === $this) {
                $assistance->setType(null);
            }
        }

        return $this;
    }
}
