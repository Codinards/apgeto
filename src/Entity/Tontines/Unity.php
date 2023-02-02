<?php

namespace App\Entity\Tontines;

use App\Entity\Main\Users\User;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\Tontines\UnityRepository;
use Doctrine\ORM\EntityManagerInterface;

// admin ManyToOne
/**
 * @ORM\Entity(repositoryClass=UnityRepository::class)
 * @ORM\Table("unity")
 */
class Unity
{
    use TontineTrait;
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $achat;

    /**
     * @ORM\Column(type="integer")
     */
    private $amount;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $benefitAt;

    /**
     * @ORM\ManyToOne(targetEntity=Tontineur::class, inversedBy="unities")
     * @ORM\JoinColumn(nullable=false)
     */
    private $tontineur;

    /**
     * @ORM\ManyToOne(targetEntity=Tontine::class, inversedBy="unities")
     * @ORM\JoinColumn(nullable=false)
     */
    private $tontine;

    /**
     * @ORM\ManyToOne(targetEntity=TontineurData::class, inversedBy="avalistes")
     */
    private $avaliste;

    /**
     * @ORM\Column(type="boolean", options={"default":false})
     */
    private $isWon = false;

    /**
     * @ORM\Column(type="boolean", options={"default":false})
     */
    private $isStopped = false;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $stoppedAt;

    /**
     * @ORM\Column(type="boolean", options={"default":false})
     */
    private $isDemiNom = false;

    /**
     * @ORM\ManyToOne(targetEntity=TontineurData::class, inversedBy="unities")
     * @ORM\JoinColumn(nullable=false)
     */
    private $tontineurData;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $observation;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="adminUnities")
     * @ORM\JoinColumn(nullable=false)
     */
    private $admin;

    /**
     * @ORM\OneToMany(targetEntity=CotisationFailure::class, mappedBy="unity", orphanRemoval=true)
     */
    private $cotisationFailures;

    public function __construct()
    {
        if ($this->createdAt === null) {
            $this->createdAt = new DateTime();
        }
        $this->cotisationFailures = new ArrayCollection();
    }



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAchat(): ?int
    {
        return $this->achat;
    }

    public function setAchat(int $achat): self
    {
        $this->achat = $achat;

        return $this;
    }

    public function getAmount(): ?int
    {
        return $this->amount;
    }

    public function setAmount(int $amount): self
    {
        $this->amount = $amount;

        return $this;
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

    public function getBenefitAt(): ?\DateTimeInterface
    {
        return $this->benefitAt;
    }

    public function setBenefitAt(?\DateTimeInterface $benefitAt): self
    {
        $this->benefitAt = $benefitAt;

        return $this;
    }

    public function resolveBenefitAt(): self
    {
        if (is_null($this->benefitAt)) {
            $this->benefitAt = new DateTime();
        }
        return $this;
    }

    public function getTontineur(): ?Tontineur
    {
        return $this->tontineur;
    }

    public function setTontineur(?Tontineur $tontineur): self
    {
        $this->tontineur = $tontineur;

        return $this;
    }

    public function getTontine(): ?Tontine
    {
        return $this->tontine;
    }

    public function setTontine(?Tontine $tontine): self
    {
        $this->tontine = $tontine;

        return $this;
    }

    public function getAvaliste(): ?TontineurData
    {
        return $this->avaliste;
    }

    public function setAvaliste(?TontineurData $avaliste): self
    {
        $this->avaliste = $avaliste;

        return $this;
    }

    public function getIsWon(): ?bool
    {
        return $this->isWon;
    }

    public function setIsWon(bool $isWon): self
    {
        $this->isWon = $isWon;

        return $this;
    }

    public function getIsStopped(): ?bool
    {
        return $this->isStopped;
    }

    public function setIsStopped(bool $isStopped): self
    {
        $this->isStopped = $isStopped;

        return $this;
    }

    public function getStoppedAt(): ?\DateTimeInterface
    {
        return $this->stoppedAt;
    }

    public function setStoppedAt(?\DateTimeInterface $stoppedAt): self
    {
        $this->stoppedAt = $stoppedAt;

        return $this;
    }

    public function getIsDemiNom(): ?bool
    {
        return $this->isDemiNom;
    }

    public function setIsDemiNom(bool $isDemiNom): self
    {
        $this->isDemiNom = $isDemiNom;

        return $this;
    }

    public function hasAchat(): bool
    {
        if (!isset($this->tontineType)) {
            $this->tontineType = $this->tontine->getType();
        }
        return !empty($this->tontineType->getMinAchat());
    }

    public function hasAvaliste(): bool
    {
        if (!isset($this->tontineType)) {
            $this->tontineType = $this->tontine->getType();
        }
        return !empty($this->tontineType->getHasAvaliste());
    }

    public function update(?int $adminId = null): self
    {
        $this->tontine->incrementWon();
        $this->tontine->close();
        $this->tontineurData->incrementWon();
        $this->benefitAt = $this->benefitAt ?? new DateTime();
        $this->isWon = true;
        if ($adminId) {
            $this->adminId = $adminId;
        }
        return $this;
    }

    public function delete(EntityManagerInterface $manager): self
    {
        $this->getTontineurData()->deleteUnity($this, $manager);
        return $this;
    }

    public function cancelBenefit(): self
    {
        $this->tontine->decrementWon();
        $this->tontine->removeClose();
        $this->tontineurData->decrementWon();
        $this->benefitAt = null;
        $this->isWon = false;
        $this->achat = null;
        $this->observation = null;
        $this->avaliste = null;
        return $this;
    }

    public function getTontineurData(): ?TontineurData
    {
        return $this->tontineurData;
    }

    public function setTontineurData(?TontineurData $tontineurData): self
    {
        $this->tontineurData = $tontineurData;

        return $this;
    }

    public function getObservation(): ?string
    {
        return $this->observation;
    }

    public function setObservation(?string $observation): self
    {
        $this->observation = $observation;

        return $this;
    }

    public function getIsCurrent(): bool
    {
        return (bool) $this->tontine->getIsCurrent();
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
     * @return Collection|CotisationStatus[]
     */
    public function getCotisationFailures(): Collection
    {
        return $this->cotisationFailures;
    }

    public function addCotisationFailure(CotisationFailure $cotisationFailure): self
    {
        if (!$this->cotisationFailures->contains($cotisationFailure)) {
            $this->cotisationFailures[] = $cotisationFailure;
            $cotisationFailure->setUnity($this);
        }

        return $this;
    }

    public function removeCotisationFailure(CotisationFailure $cotisationFailure): self
    {
        if ($this->cotisationFailures->removeElement($cotisationFailure)) {
            // set the owning side to null (unless already changed)
            if ($cotisationFailure->getUnity() === $this) {
                $cotisationFailure->setUnity(null);
            }
        }
        return $this;
    }
}
