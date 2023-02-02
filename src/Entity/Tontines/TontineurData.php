<?php

namespace App\Entity\Tontines;

use App\Entity\Main\Users\User;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use App\Form\Extensions\Validations\UniqueEntity;
use App\Repository\Tontines\TontineurDataRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @ORM\Entity(repositoryClass=TontineurDataRepository::class)
 * @UniqueEntity(fields={"tontine","tontineur"})
 * @ORM\Table("tontineur_data")
 */
class TontineurData
{
    use TontineTrait;
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Tontineur::class, inversedBy="tontineurData")
     * @ORM\JoinColumn(nullable=false)
     */
    private $tontineur;

    /**
     * @ORM\ManyToOne(targetEntity=Tontine::class, inversedBy="tontineurData")
     * @ORM\JoinColumn(nullable=false)
     */
    private $tontine;

    /**
     * @ORM\Column(type="integer")
     */
    private $count;

    /**
     * @ORM\Column(type="integer")
     */
    private $won = 0;

    /**
     * @ORM\Column(type="integer")
     */
    private $lockedCount = 0;

    /**
     * @ORM\OneToMany(targetEntity=Unity::class, mappedBy="avaliste")
     */
    private $avalistes;

    /**
     * @ORM\OneToMany(targetEntity=Unity::class, mappedBy="tontineurData", orphanRemoval=true, cascade={"persist", "remove"})
     */
    private $unities;

    private $demiNom = false;

    /**
     * @ORM\Column(type="integer")
     */
    private $countDemiNom = 0;

    /**
     * @ORM\Column(type="boolean", options={"default":false})
     */
    private $isLocked = false;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $lockedAt;

    private $isSelected = false;

    /**
     * @ORM\OneToMany(targetEntity=CotisationFailure::class, mappedBy="tontiner")
     */
    private $cotisationFailures;


    public function __construct()
    {
        $this->unities = new ArrayCollection();
        $this->avalistes = new ArrayCollection();
        if ($this->countDemiNom !== 0) {
            $this->demiNom = true;
        }
        $this->cotisationFailures = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getCount(): ?int
    {
        return $this->count;
    }

    public function setCount(?int $count): self
    {
        $this->count = (int) $count;

        return $this;
    }

    public function getWon(): ?int
    {
        return $this->won;
    }

    public function setWon(int $won): self
    {
        $this->won = $won;

        return $this;
    }

    public function getLockedCount(): ?int
    {
        return $this->lockedCount;
    }

    public function setLockedCount(int $lockedCount): self
    {
        $this->lockedCount = $lockedCount;

        return $this;
    }

    /**
     * @return Collection|Unity[]
     */
    public function getUnities(): Collection
    {
        return $this->unities;
    }

    public function addUnity(Unity $unity, bool $incrementCount = true): self
    {
        if (!$this->unities->contains($unity)) {
            $this->unities[] = $unity;
            $unity->setTontineurData($this);
            if ($incrementCount) {
                $this->incrementCount();
            }
        }

        return $this;
    }


    public function removeUnity(Unity $unity): self
    {
        if ($this->unities->removeElement($unity)) {
            // set the owning side to null (unless already changed)
            if ($unity->getTontineurData() === $this) {
                $unity->setTontineurData(null);
            }
            $this->decrementCount();
        }
        return $this;
    }

    public function getDemiNom()
    {
        return $this->demiNom;
    }

    public function setDemiNom(bool $demiNom): self
    {
        $this->demiNom = $demiNom;

        return $this;
    }

    /**
     * @return Collection|Unity[]
     */
    public function getAvalistes(): Collection
    {
        return $this->avalistes;
    }

    public function addAvaliste(Unity $avaliste): self
    {
        if (!$this->avalistes->contains($avaliste)) {
            $this->avalistes[] = $avaliste;
            $avaliste->setAvaliste($this);
        }

        return $this;
    }

    public function removeAvaliste(Unity $avaliste): self
    {
        if ($this->avalistes->removeElement($avaliste)) {
            // set the owning side to null (unless already changed)
            if ($avaliste->getAvaliste() === $this) {
                $avaliste->setAvaliste(null);
            }
        }

        return $this;
    }

    public function hydrateUnities(int $amount, User $adminId, int $count = 1, bool $incrementCount = true): self
    {

        for ($i = 1; $i <= $count; $i++) {
            $this->addUnity(
                $unity = (new Unity)
                    ->setAmount($amount)
                    ->setTontineur($this->tontineur)
                    ->setTontine($this->tontine)
                    ->setAdmin($adminId),
                $incrementCount
            );
            $this->tontine->incrementCount();
            $this->tontine->incrementCotisation($unity->getAmount());
        }
        if ($this->demiNom) {
            $this->addUnity(
                $unity = (new Unity)
                    ->setAmount($amount / 2)
                    ->setTontineur($this->tontineur)
                    ->setTontine($this->tontine)
                    ->setAdmin($adminId)
                    ->setIsDemiNom(true),
                $incrementCount
            );
            $this->setDemiNom(true);
            $this->tontine->incrementCount();
            $this->incrementCountDemiNom();
            //$this->tontine->incrementMaxCount();
            $this->tontine->incrementCountDemiNom();
            $this->tontine->incrementCotisation($unity->getAmount());
        }

        if ($this->count > $this->tontine->getMaxCount()) {
            $this->tontine->setMaxCount($this->count);
        }
        return $this;
    }

    public function mergeUnities(int $amount, User $admin, int $count = 1, bool $incrementCount = true, bool $demiNom = false): self
    {

        for ($i = 1; $i <= $count; $i++) {
            $this->addUnity(
                $unity = (new Unity)
                    ->setAmount($amount)
                    ->setTontineur($this->tontineur)
                    ->setTontine($this->tontine)
                    ->setAdmin($admin),
                $incrementCount
            );
            $this->tontine->incrementCount();
            $this->tontine->incrementCotisation($unity->getAmount());
        }
        if ($demiNom) {
            if ($this->countDemiNom === 0) {
                $this->addUnity(
                    $unity = (new Unity)
                        ->setAmount($amount / 2)
                        ->setTontineur($this->tontineur)
                        ->setTontine($this->tontine)
                        ->setAdmin($admin)
                        ->setIsDemiNom(true),
                    $incrementCount
                );
                $this->tontine->incrementCount();
                $this->incrementCountDemiNom();
                //$this->tontine->incrementMaxCount();
                $this->tontine->incrementCountDemiNom();
                $this->tontine->incrementCotisation($unity->getAmount());
            } else {
                /** @var Unity $unity */
                foreach ($this->getUnities() as $key => $unity) {
                    if ($unity->getIsDemiNom()) {
                        unset($this->unities[$key]);
                        $this->decrementCountDemiNom();
                        $this->decrementCount();
                        $this->tontine->decrementCount();
                        $this->tontine->decrementCountDemiNom();
                    }
                }
                $this->addUnity(
                    $unity = (new Unity)
                        ->setAmount($amount)
                        ->setTontineur($this->tontineur)
                        ->setTontine($this->tontine)
                        ->setAdmin($admin),
                    $incrementCount
                );
                $this->tontine->incrementCount();
                $this->tontine->incrementCotisation($unity->getAmount());
            }
        }

        if ($this->count > $this->tontine->getMaxCount()) {
            $this->tontine->setMaxCount($this->count);
        }
        return $this;
    }

    public function equals(self $tontineurData): bool
    {
        $tontine = $tontineurData->getTontine();
        $tontineur = $tontineurData->getTontineur();
        return $tontineurData == $this ||
            ($tontine->getId() === $this->getTontine()->getId() &&
                $tontineur->getUser()->getId() === $this->getTontineur()->getUser()->getId());
    }

    public function incrementCount(): self
    {
        $this->count++;

        return $this;
    }

    public function decrementCount(): self
    {
        $this->count--;

        return $this;
    }

    public function incrementWon(): self
    {
        $this->won++;
        return $this;
    }

    public function decrementWon(): self
    {
        $this->won--;
        return $this;
    }

    public function incrementCountDemiNom(): self
    {

        $this->countDemiNom++;

        return $this;
    }

    public function decrementCountDemiNom(): self
    {
        $this->countDemiNom--;

        return $this;
    }

    public function __toString()
    {
        return (string) $this->tontineur;
    }


    public function incrementLockedCount()
    {
        $this->lockedCount++;
    }

    public function decrementLockedCount()
    {
        if ($this->lockedCount >= 1) {
            $this->lockedCount--;
        }
    }

    public function getCountDemiNom(): ?int
    {
        return $this->countDemiNom;
    }

    public function setCountDemiNom(int $countDemiNom): self
    {
        $this->countDemiNom = $countDemiNom;

        return $this;
    }

    public function getIsLocked(): ?bool
    {
        return $this->isLocked;
    }

    public function setIsLocked(bool $isLocked): self
    {
        $this->isLocked = $isLocked;

        return $this;
    }

    public function getLockedAt(): ?\DateTimeInterface
    {
        return $this->lockedAt;
    }

    public function setLockedAt(?\DateTimeInterface $lockedAt): self
    {
        $this->lockedAt = $lockedAt;

        return $this;
    }

    public function getAmount(): int
    {
        return $this->collection($this->getUnities()->toArray())
            ->sum(fn (Unity $item) => $item->getIsStopped() ? 0 : $item->getAmount());
    }

    public function getBalanceAmount(): int
    {
        return $this->collection($this->getUnities()->toArray())
            ->sum(fn (Unity $item) => ($item->getIsStopped() || $item->getIsWon()) ? 0 : $item->getAmount());
    }

    /**
     * Get the value of isSelected
     */
    public function getIsSelected()
    {
        return $this->isSelected;
    }

    /**
     * Set the value of isSelected
     *
     * @return  self
     */
    public function setIsSelected($isSelected)
    {
        $this->isSelected = $isSelected;

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
            $cotisationFailure->setTontiner($this);
        }

        return $this;
    }

    public function removeCotisationFailure(CotisationFailure $cotisationFailure): self
    {
        if ($this->cotisationFailures->removeElement($cotisationFailure)) {
            // set the owning side to null (unless already changed)
            if ($cotisationFailure->getTontiner() === $this) {
                $cotisationFailure->setTontiner(null);
            }
        }

        return $this;
    }

    public function deleteUnity(Unity $unity, EntityManagerInterface $manager): self
    {
        $tontine = $unity->getTontine();
        $tontine->decrementCount();
        $tontine->decrementCotisation($unity->getAmount());
        $this->decrementCount();
        if ($unity->getIsDemiNom()) {
            $tontine->decrementCountDemiNom();
            $this->decrementCountDemiNom();
        }
        $manager->remove($unity);
        if ($this->getCount() === 0) {
            $manager->remove($this);
        }
        return $this;
    }
}
