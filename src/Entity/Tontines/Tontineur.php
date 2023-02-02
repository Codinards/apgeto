<?php

namespace App\Entity\Tontines;

use App\Entity\Main\Users\User;
use Doctrine\ORM\Mapping as ORM;
use App\Tools\Entity\StaticEntityManager;
use Doctrine\Common\Collections\Collection;
use App\Repository\Tontines\TontineurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Constraints;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;


/**
 * @ORM\Entity(repositoryClass=TontineurRepository::class)
 * @UniqueEntity(fields={"userId"})
 * @ORM\Table("tontineur")
 */
class Tontineur
{
    use TontineTrait;
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity=TontineurData::class, mappedBy="tontineur", orphanRemoval=true)
     */
    private $tontineurData;

    private $unities;

    /**
     * @ORM\ManyToMany(targetEntity=Tontine::class, mappedBy="tontineurs")
     */
    private $tontines;

    /**
     * @ORM\OneToOne(targetEntity=User::class, inversedBy="tontineur", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="adminTontineurs")
     * @ORM\JoinColumn(nullable=true)
     */
    private $admin;



    public function __construct()
    {
        $this->unities = new ArrayCollection();
        $this->tontineurData = new ArrayCollection();
        $this->tontines = new ArrayCollection();
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
            $unity->setTontineur($this);
            /*if ($incrementCount) {
                $this->count++;
            }*/
        }

        return $this;
    }

    public function removeUnity(Unity $unity): self
    {
        if ($this->unities->removeElement($unity)) {
            // set the owning side to null (unless already changed)
            if ($unity->getTontineur() === $this) {
                $unity->setTontineur(null);
                return $this; //->decrementCount();
            }
        }

        return $this;
    }

    public function __toString()
    {
        return (string) $this->name;
    }


    public function equals(Tontineur $tontineur): bool
    {
        return $tontineur === $this || ($tontineur->getUser()->getId() == $this->getUser()->getId());
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
            $tontineurData->setTontineur($this);
        }

        return $this;
    }

    public function removeTontineurData(TontineurData $tontineurData): self
    {
        if ($this->tontineurData->removeElement($tontineurData)) {
            // set the owning side to null (unless already changed)
            if ($tontineurData->getTontineur() === $this) {
                $tontineurData->setTontineur(null);
            }
        }

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
            $tontine->addTontineur($this);
        }

        return $this;
    }

    public function removeTontine(Tontine $tontine): self
    {
        if ($this->tontines->removeElement($tontine)) {
            $tontine->removeTontineur($this);
        }

        return $this;
    }

    public function getCurrentData(): ArrayCollection
    {
        return $this->getTontineurData()
            ->filter(
                fn (TontineurData $item) => $item->getTontine()->getIsCurrent()
            );
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

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
}
