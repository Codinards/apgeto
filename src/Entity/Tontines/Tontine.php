<?php

namespace App\Entity\Tontines;

use App\Entity\Main\Users\User;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use App\Repository\Tontines\TontineRepository;
use App\Entity\EntityTraits\GlobalMethodsTrait;
use App\Entity\Exceptions\EntityException;
use Doctrine\Common\Collections\ArrayCollection;
use App\Entity\Exceptions\TontineOperationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraints as Constraints;

// admin ManyToOne
/**
 * @ORM\Entity(repositoryClass=TontineRepository::class)
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table("tontine")
 */
class Tontine
{
    use GlobalMethodsTrait, TontineTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Tontinetype::class, inversedBy="tontines")
     * @ORM\JoinColumn(nullable=false)
     */
    private $type;

    /**
     * @ORM\Column(type="integer")
     * @Constraints\GreaterThanOrEqual(0)
     */
    private $cotisation;

    /**
     * @ORM\Column(type="integer")
     * @Constraints\GreaterThanOrEqual(0)
     */
    private $amount;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isCurrent = true;

    /**
     * @ORM\Column(type="integer", options={"default":0})
     */
    private $count = 0;

    /**
     * @ORM\Column(type="integer", options={"default":1})
     */
    private $maxCount = 1;

    /**
     * @ORM\Column(type="integer", options={"default":0})
     */
    private $won = 0;

    /**
     * @ORM\Column(type="integer", options={"default":0})
     */
    private $countDeminNom = 0;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $closedAt;

    /**
     * @ORM\OneToMany(targetEntity=Unity::class, mappedBy="tontine", orphanRemoval=true)
     */
    private $unities;


    /**
     * @ORM\OneToMany(targetEntity=TontineurData::class, mappedBy="tontine", orphanRemoval=true, cascade={"persist", "remove"})
     */
    private $tontineurData;

    /**
     * @ORM\ManyToMany(targetEntity=Tontineur::class, inversedBy="tontines")
     */
    private $tontineurs;

    /**
     * @ORM\Column(type="boolean", options={"default":true})
     */
    private $addMember = 1;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @ORM\Column(type="integer", options={"default":0})
     */
    private $lockedCount = 0;

    /**
     * @ORM\Column(type="integer", options={"default":0})
     */
    private $DemiLockedCount = 0;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="adminTontines")
     * @ORM\JoinColumn(nullable=false)
     */
    private $admin;

    /**
     * @ORM\OneToMany(targetEntity=CotisationFailure::class, mappedBy="tontine")
     */
    private $cotisationFailures;


    public function __construct()
    {
        $this->unities = new ArrayCollection();
        $this->tontineurData = new ArrayCollection();
        $this->tontineurs = new ArrayCollection();
        if ($this->createdAt === null) {
            $this->createdAt = new DateTime();
        }
        $this->cotisationFailures = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?Tontinetype
    {
        return $this->type;
    }

    public function setType(?Tontinetype $type): self
    {
        $this->type = $type;

        return $this;
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

    public function getAmount(): ?int
    {
        return $this->amount;
    }

    public function setAmount(int $amount): self
    {
        $this->amount = $amount;

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

    public function getCount(): ?int
    {
        return $this->count;
    }

    public function setCount(int $count): self
    {
        $this->count = $count;

        return $this;
    }

    public function getMaxCount(): ?int
    {
        return $this->maxCount;
    }

    public function setMaxCount(int $maxCount): self
    {
        $this->maxCount = $maxCount;

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

    public function getCountDeminNom(): ?int
    {
        return $this->countDeminNom;
    }

    public function setCountDeminNom(int $countDeminNom): self
    {
        $this->countDeminNom = $countDeminNom;

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

    public function getClosedAt(): ?\DateTimeInterface
    {
        return $this->closedAt;
    }

    public function setClosedAt(?\DateTimeInterface $closedAt): self
    {
        $this->closedAt = $closedAt;

        return $this;
    }


    /**
     * @return Collection|Unity[]
     */
    public function getUnities(): Collection
    {
        return $this->unities;
    }

    public function addUnity(Unity $unity): self
    {
        if (!$this->unities->contains($unity)) {
            $this->unities[] = $unity;
            $unity->setTontine($this);
        }

        return $this;
    }

    public function removeUnity(Unity $unity): self
    {
        if ($this->unities->removeElement($unity)) {
            // set the owning side to null (unless already changed)
            if ($unity->getTontine() === $this) {
                $unity->setTontine(null);
            }
        }

        return $this;
    }

    public function dataHasTontineur(int $tontineur, bool $fillUnities = false): bool
    {
        /** @var TontineurData $data */
        foreach ($this->getTontineurData() as $data) {
            if ($fillUnities and $data->getUnities()->isEmpty()) null;
            if ($data->getTontineur()->getId() === $tontineur) return true;
        }
        return false;
    }

    public function noHasDuplicateTontineur(Tontineur $tontineur): bool
    {
        /** @var Tontineur $membre */
        foreach ($this->tontineurs as $membre) {

            if ($membre->getUser() == $user = $tontineur->getUser()) {
                throw new TontineOperationException(
                    "Le membre " . $user->getUsername() . " fait déja partie de cette tontine"
                );
            }
        }
        return true;
    }

    public function hydrateTontineur(array $tontineurs): self
    {
        foreach ($tontineurs as $user) {
            if ($user instanceof Tontineur) {
                $this->addTontineur($user);
            }
        }

        return $this;
    }

    public function hydrateDataFromTontineur(array $tontineurs): self
    {
        foreach ($tontineurs as $tontineur) {
            if ($tontineur instanceof Tontineur) {
                $this->addTontineur($tontineur);
                $this->addTontineurData(
                    (new TontineurData)
                        ->setTontine($this)
                        ->setTontineur($tontineur)
                );
            }
        }
        return $this;
    }

    public function deleteTontineurByIndex(int $index)
    {
        unset($this->tontineurs[$index]);
    }

    public function setTontineurs(ArrayCollection $tontineurs): self
    {
        $this->tontineurs = $tontineurs;
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

    public function isClosed(): bool
    {
        return $this->count === $this->won;
    }

    public function close(): self
    {
        if ($this->isClosed()) {
            $this->isCurrent = false;
            $this->closedAt = new DateTime();
        }
        return $this;
    }

    public function removeClose(): self
    {
        if ($this->closedAt !== null) {
            $this->closedAt = null;
        }
        if ($this->isCurrent === false) {
            $this->isCurrent == true;
        }
        return $this;
    }

    public function incrementMaxCount(): self
    {
        $this->maxCount++;

        return $this;
    }

    public function setData(Tontine $tontine)
    {
        $this->id = null;
        $this->tontineurs = new ArrayCollection();
        $this->unities = new ArrayCollection();
        $this->count = 0;
        $this->won = 0;
        $this->maxCount = 1;
        $this->type = $tontine->getType();
        $this->setAdmin($tontine->getAdmin());
    }

    public function hasTontineur(Tontineur $tontineur, bool $returnBool = false): bool| Tontineur
    {
        /** @var Tontineur $tontineTontineur */
        foreach ($this->tontineurs as $tontineTontineur) {
            if ($tontineTontineur->equals($tontineur)) {
                return $returnBool ? true : $tontineTontineur;
            }
        }
        return false;
    }

    public function hasTontineurData(TontineurData $data)
    {
        /** @var TontineurData $tontineTontineur */
        foreach ($this->tontineurData as $tontineTontineur) {
            if ($tontineTontineur->equals($data)) {
                return $tontineTontineur;
            }
        }
        return false;
    }


    public function incrementCountDemiNom(): self
    {
        $this->countDeminNom++;

        return $this;
    }

    public function decrementCountDemiNom(): self
    {
        $this->countDeminNom--;

        return $this;
    }

    public function incrementCotisation(int $cotisation)
    {
        $this->cotisation += $cotisation;
    }

    public function decrementCotisation(int $cotisation)
    {
        $this->cotisation -= $cotisation;
    }

    /**
     * @return Collection|TontineurData[]
     */
    public function getTontineurData(): Collection
    {
        return $this->tontineurData;
    }

    public function addTontineurData(TontineurData $tontineurData): self
    {
        if (!$this->tontineurData->contains($tontineurData)) {
            $this->tontineurData[] = $tontineurData;
            $tontineurData->setTontine($this);
        }

        return $this;
    }

    public function setTontineurData(ArrayCollection $tontineurData): self
    {
        $this->tontineurData = $tontineurData;

        return $this;
    }

    public function removeTontineurData(TontineurData $tontineurData): self
    {
        if ($this->tontineurData->removeElement($tontineurData)) {
            // set the owning side to null (unless already changed)
            if ($tontineurData->getTontine() === $this) {
                $tontineurData->setTontine(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Tontineur[]
     */
    public function getTontineurs(): Collection
    {
        return $this->tontineurs;
    }

    public function addTontineur(Tontineur $tontineur): self
    {
        if (!$this->tontineurs->contains($tontineur)) {
            $this->tontineurs[] = $tontineur;
        }

        return $this;
    }

    public function removeTontineur(Tontineur $tontineur): self
    {
        $this->tontineurs->removeElement($tontineur);

        return $this;
    }

    public function getWonUnities(): ArrayCollection
    {
        return $this->getUnities()->filter(fn (Unity $unity) => $unity->getIsWon() == true);
    }

    public function getNotWonUnities(): ArrayCollection
    {
        return $this->getUnities()->filter(fn (Unity $unity) => $unity->getIsWon() == false);
    }

    public function getAddMember(): ?bool
    {
        return $this->addMember;
    }

    public function setAddMember(bool $addMember): self
    {
        $this->addMember = $addMember;

        return $this;
    }

    public function dataCount()
    {
        return $this->tontineurData->count();
    }

    public function tontineursCount()
    {
        return $this->tontineurs->count();
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

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

    public function getDemiLockedCount(): ?int
    {
        return $this->DemiLockedCount;
    }

    public function setDemiLockedCount(int $DemiLockedCount): self
    {
        $this->DemiLockedCount = $DemiLockedCount;

        return $this;
    }

    public function incrementLockedCount(): self
    {
        $this->lockedCount++;

        return $this;
    }

    public function decrementLockedCount(): self
    {
        if ($this->lockedCount < 0) {
            throw new EntityException(get_class($this) . '::' . 'lockedCount property can not be negative');
        }

        $this->lockedCount--;

        return $this;
    }

    public function incrementDemiLockedCount(): self
    {
        $this->DemiLockedCount++;

        return $this;
    }

    public function decrementDemiLockedCount(): self
    {
        if ($this->DemiLockedCount < 0) {
            throw new EntityException(get_class($this) . '::' . 'DemiLockedCount property can not be negative');
        }

        $this->DemiLockedCount--;

        return $this;
    }

    public function getRealCount(): float
    {
        return ($this->count - $this->lockedCount) - ($this->countDeminNom - $this->DemiLockedCount) + ($this->countDeminNom - $this->DemiLockedCount) * 0.5;
    }

    public function getRealDemiCount(): float
    {
        return $this->countDeminNom - $this->DemiLockedCount;
    }

    public function totalLockedCount(): int
    {
        return (int) ($this->lockedCount + $this->DemiLockedCount);
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
            $cotisationFailure->setTontine($this);
        }

        return $this;
    }

    public function removeCotisationFailure(CotisationFailure $cotisationFailure): self
    {
        if ($this->cotisationFailures->removeElement($cotisationFailure)) {
            // set the owning side to null (unless already changed)
            if ($cotisationFailure->getTontine() === $this) {
                $cotisationFailure->setTontine(null);
            }
        }

        return $this;
    }

    public function delete(EntityManagerInterface $manager): self
    {
        $tontineurData = $manager->getRepository(TontineurData::class)->findBy(["tontine" => $this]);
        foreach ($tontineurData as $data) {
            foreach ($data->getUnities() as $unity) {
                $manager->remove($unity);
            }
            $manager->remove($data);
        }
        $failures = $manager->getRepository(CotisationFailure::class)->findBy(["tontine" => $this]);
        foreach ($failures as $failure) {
            $manager->remove($failure);
        }
        // $this->setIsCurrent(false);
        // $this->setData($this);
        // $manager->flush();
        $manager->remove($this);
        return $this;
    }
}
