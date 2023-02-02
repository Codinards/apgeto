<?php

namespace App\Entity\Tontines;

use App\Repository\Tontines\CotisationDayRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CotisationDayRepository::class)
 */
class CotisationDay
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="date")
     */
    private $createdAt;

    /**
     * @ORM\OneToMany(targetEntity=CotisationFailure::class, mappedBy="cotisationDay")
     */
    private $cotisationFailures;

    public function __construct()
    {
        $this->cotisationFailures = new ArrayCollection();
        $this->createdAt = new DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return Collection|CotisationFailure[]
     */
    public function getCotisationFailures(): Collection
    {
        return $this->cotisationFailures;
    }

    public function addCotisationFailure(CotisationFailure $cotisationFailure): self
    {
        if (!$this->cotisationFailures->contains($cotisationFailure)) {
            $this->cotisationFailures[] = $cotisationFailure;
            $cotisationFailure->setCotisationDay($this);
        }

        return $this;
    }

    public function removeCotisationFailure(CotisationFailure $cotisationFailure): self
    {
        if ($this->cotisationFailures->removeElement($cotisationFailure)) {
            // set the owning side to null (unless already changed)
            if ($cotisationFailure->getCotisationDay() === $this) {
                $cotisationFailure->setCotisationDay(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->createdAt;
    }
}
