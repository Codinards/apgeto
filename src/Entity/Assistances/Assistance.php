<?php

namespace App\Entity\Assistances;

use App\Entity\Main\Funds\Fund;
use App\Entity\Main\Users\User;
use App\Repository\Assistances\AssistanceRepository;
use App\Tools\Entity\StaticEntityManager;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Illuminate\Support\Collection as SupportCollection;

/**
 * @ORM\Entity(repositoryClass=AssistanceRepository::class)
 * @ORM\Table("assistance")
 */
class Assistance
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=AssistanceType::class, inversedBy="assistances")
     * @ORM\JoinColumn(nullable=false)
     */
    private $type;

    /**
     * @ORM\Column(type="integer")
     */
    private $amount;


    /**
     * @ORM\Column(type="datetime", name="created_at")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="integer", name="total_contributions")
     */
    private $totalContributions;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $wording;

    /**
     * @ORM\Column(type="string", length=4)
     */
    private $year;

    /**
     * @ORM\OneToMany(targetEntity=Contributor::class, mappedBy="assistance", orphanRemoval=true, cascade={"persist"})
     */
    private $contributors;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="assistances")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="adminAssistances")
     * @ORM\JoinColumn(nullable=false)
     */
    private $admin;

    public function __construct()
    {
        if (is_null($this->createdAt)) {
            $this->createdAt = new DateTime();
            $this->year = $this->createdAt->format('Y');
        }

        $this->contributors = new ArrayCollection();
        $this->funds = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }


    public function getType(): ?AssistanceType
    {
        return $this->type;
    }

    public function setType(?AssistanceType $type): self
    {
        $this->type = $type;

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

    public function updateAmount(int $amount): self
    {
        $this->amount = $amount;

        foreach ($this->contributors as $contributor) {
            $contributor->setAmount($this->amount);
        }

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

    public function getTotalContributions(): ?int
    {
        return $this->totalContributions;
    }

    public function setTotalContributions(int $totalContributions): self
    {
        $this->totalContributions = $totalContributions;

        return $this;
    }

    public function incrementTotalContribution(int $amount): self
    {
        $this->totalContributions += $amount;
        return $this;
    }

    public function decrementTotalContribution(int $amount): self
    {
        $this->totalContributions -= $amount;
        return $this;
    }

    public function hydrateContributorsFromUsers(array $users): self
    {
        /** @var User[] $users */
        foreach ($users as $key => $user) {
            $this->contributors->add(
                (new Contributor)
                    ->setUser($user)
                    ->setAmount($this->amount ?? 0)
                    ->setSelect(true)
                    ->mergeAccount($user->getAccount())
                    ->setIndex($key)
                    ->setAssistance($this)
            );
        }
        return $this;
    }

    public function reinitializeContributor(): self
    {
        $this->contributors = new ArrayCollection();
        return $this;
    }

    public function filterContributors(): self
    {
        $this->contributors = $this->contributors->filter(
            function (Contributor $contributor) {
                $isSelected = $contributor->getSelect();
                if ($isSelected) $this->totalContributions += $contributor->getAmount();
                return $isSelected;
            }
        );
        // if ($this->getType()->getAmountType() === 2) {
        //     $this->totalContributions = $this->getType()->getAmount();
        // }
        if ($this->getType()->getAmountType() === 3) $this->amount = 0;

        return $this;
    }

    public function getWording(): ?string
    {
        return $this->wording;
    }

    public function setWording(string $wording): self
    {
        $this->wording = $wording;

        return $this;
    }


    public function getYear(): ?string
    {
        return $this->year;
    }

    public function setYear(?string $year): self
    {
        $this->year = $year;

        return $this;
    }

    /**
     * @return Collection|Contributor[]
     */
    public function getContributors(bool $entities = false): Collection
    {
        return $this->contributors;
    }

    public function addContributor(Contributor $contributor): self
    {
        if (!$this->contributors->contains($contributor)) {
            $this->contributors[] = $contributor;
            $contributor->setAssistance($this);
        }

        return $this;
    }

    public function removeContributor(Contributor $contributor): self
    {
        if ($this->contributors->removeElement($contributor)) {
            // set the owning side to null (unless already changed)
            if ($contributor->getAssistance() === $this) {
                $contributor->setAssistance(null);
            }
        }

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
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
