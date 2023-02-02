<?php

namespace App\Entity\Tontines;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use App\Form\Extensions\Validations\UniqueEntity;
use App\Repository\Tontines\TontinetypeRepository;
use Symfony\Component\Validator\Constraints as Constraints;

/**
 * @ORM\Entity(repositoryClass=TontinetypeRepository::class)
 * @UniqueEntity("name")
 * @ORM\Table("tontine_type")
 */
class Tontinetype
{
    use TontineTrait;
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     * @Constraints\GreaterThanOrEqual(0)
     */
    private $cotisation;

    /**
     * @ORM\Column(type="integer")
     * @Constraints\GreaterThanOrEqual(0)
     */
    private $minAchat = 0;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Constraints\NotBlank
     */
    private $name;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isCurrent = true;

    /**
     * @ORM\Column(type="boolean", options={"default":true})
     */
    private $hasAvaliste = 1;

    /**
     * @ORM\Column(type="integer", options={"default":0})
     */
    private $amend = 0;

    /**
     * @ORM\Column(type="integer", options={"default":0})
     */
    private $minAmend = 0;

    /**
     * @ORM\OneToMany(targetEntity=Tontine::class, mappedBy="type", orphanRemoval=true)
     */
    private $tontines;

    /**
     * @ORM\Column(type="boolean", options={"default":false})
     */
    private $hasAchat = false;

    /**
     * @ORM\Column(type="boolean", options={"default":false})
     */
    private $hasMultipleTontine = false;

    public function __construct()
    {
        $this->tontines = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCotisation(): ?int
    {
        return $this->cotisation;
    }

    public function setCotisation(int $cotisation): self
    {
        $this->cotisation = $cotisation;

        return $this;
    }

    public function getMinAchat(): ?int
    {
        return $this->minAchat;
    }

    public function setMinAchat(int $minAchat): self
    {
        $this->minAchat = $minAchat;

        return $this;
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

    public function getIsCurrent(): ?bool
    {
        return $this->isCurrent;
    }

    public function setIsCurrent(bool $isCurrent): self
    {
        $this->isCurrent = $isCurrent;

        return $this;
    }

    public function getHasAvaliste(): ?bool
    {
        return $this->hasAvaliste;
    }

    public function setHasAvaliste(bool $hasAvaliste): self
    {
        $this->hasAvaliste = $hasAvaliste;

        return $this;
    }

    public function getAmend(): ?int
    {
        return $this->amend;
    }

    public function setAmend(int $amend): self
    {
        $this->amend = $amend;

        return $this;
    }

    public function getMinAmend(): ?int
    {
        return $this->minAmend;
    }

    public function setMinAmend(int $minAmend): self
    {
        $this->minAmend = $minAmend;

        return $this;
    }

    /**
     * @return Collection|Tontine[]
     */
    public function getTontines(): Collection
    {
        return $this->tontines;
    }

    public function addTontine(Tontine $tontine): self
    {
        if (!$this->tontines->contains($tontine)) {
            $this->tontines[] = $tontine;
            $tontine->setType($this);
        }

        return $this;
    }

    public function removeTontine(Tontine $tontine): self
    {
        if ($this->tontines->removeElement($tontine)) {
            // set the owning side to null (unless already changed)
            if ($tontine->getType() === $this) {
                $tontine->setType(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->name;
    }

    public function getHasAchat(): ?bool
    {
        return $this->hasAchat;
    }

    public function setHasAchat(bool $hasAchat): self
    {
        $this->hasAchat = $hasAchat;

        return $this;
    }

    public function getHasMultipleTontine(): ?bool
    {
        return $this->hasMultipleTontine;
    }

    public function setHasMultipleTontine(bool $hasMultipleTontine): self
    {
        $this->hasMultipleTontine = $hasMultipleTontine;

        return $this;
    }
}
