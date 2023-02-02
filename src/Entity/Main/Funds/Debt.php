<?php

namespace App\Entity\Main\Funds;

use App\Entity\Interests\UserInterest;
use App\Entity\Main\Users\User;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use App\Entity\EntityTraits\GlobalMethodsTrait;
use App\Repository\Main\Funds\DebtRepository;
use DateTime;
use DateTimeInterface;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @ORM\Entity(repositoryClass=DebtRepository::class)
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table("debt")
 */
class Debt
{
    use GlobalMethodsTrait;

    const FROM_RENEWAL = 'renewal';
    const FROM_DEBT = 'normal';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * 
     */
    private int $id;

    /**
     * @ORM\Column(type="string", length=255)
     *
     */
    private ?string $wording = null;

    /**
     * @ORM\Column(type="integer", name="loan_in_flows", nullable=true)
     *
     */
    private ?int $loanInFlows = null;

    /**
     * @ORM\Column(type="integer", name="loan_out_flows", nullable=true)
     *
     */
    private ?int $loanOutFlows = null;

    /**
     * @ORM\Column(type="integer", name="loan_renewals", nullable=true)
     *
     */
    private ?int $loanRenewals = null;

    /**
     * @ORM\Column(type="integer", name="loan_balances", options={"default":0})
     * 
     */
    private int $loanBalances = 0;

    /**
     * @ORM\Column(type="integer", nullable=true)
     *
     */
    private ?int $interests = null;

    /**
     * @ORM\Column(type="text", nullable=true)
     *
     */
    private ?string $observations = null;

    /**
     * @ORM\ManyToOne(targetEntity=Account::class, inversedBy="debts")
     * @ORM\JoinColumn(nullable=false)
     *
     */
    private ?Account $account = null;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="debts")
     * @ORM\JoinColumn(nullable=false)
     * 
     */
    private ?User $user = null;


    /**
     * @ORM\Column(type="string", length=4)
     * 
     */
    private null|int|string $year = null;

    /**
     * @ORM\ManyToOne(targetEntity=Debt::class, inversedBy="children")
     *
     */
    private ?Debt $parent = null;

    /**
     * @ORM\OneToMany(targetEntity=Debt::class, mappedBy="parent")
     */
    private Collection $children;

    /**
     * @ORM\Column(type="datetime")
     * 
     */
    private ?DateTimeInterface $createdAt = null;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="adminDebts")
     * @ORM\JoinColumn(nullable=false)
     *
     */
    private ?User $admin = null;

    /**
     * @ORM\Column(type="boolean", options={"default":true})
     */
    private bool $isCurrent = true;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?DateTimeInterface $renewalAt = null;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?DateTimeInterface $paybackAt = null;

    /**
     * @ORM\OneToMany(targetEntity=DebtInterest::class, mappedBy="debt", orphanRemoval=true)
     */
    private Collection $debtInterests;

    /**
     * @ORM\Column(type="integer")
     */
    private int $previousBalances = 0;

    /**
     * @ORM\OneToMany(targetEntity=UserInterest::class, mappedBy="debt")
     */
    private $userInterests;

    /**
     * @ORM\OneToMany(targetEntity=DebtRenewal::class, mappedBy="debt")
     */
    private $debtRenewals;

    /**
     * @ORM\Column(type="dateinterval", nullable=true)
     */
    private $renewalPeriod;

    /**
     * @ORM\Column(type="integer", options={"default":0})
     */
    private $previousTotalInflows = 0;

    /**
     * @ORM\Column(type="integer", options={"default":0})
     */
    private $previousTotalOutflows = 0;

    /**
     * @var \DateTimeInterface
     */
    private $renewalDate;

    /**
     * @var boolean
     */
    private $isRenewable = false;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $debtRate;

    /**
     * @ORM\OneToOne(targetEntity=DebtRenewal::class, mappedBy="linkedDebt", cascade={"persist", "remove"})
     */
    private $linkedDebtRenewal;

    /**
     * @ORM\OneToMany(targetEntity=DebtAvalist::class, mappedBy="debt", cascade={"persist", "remove"})
     */
    private $avalistes;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $type;

    public string $origin = self::FROM_DEBT;



    public function __construct()
    {
        $this->children = new ArrayCollection();
        $this->debtInterests = new ArrayCollection();
        $this->userInterests = new ArrayCollection();
        $this->debtRenewals = new ArrayCollection();
        $this->avalistes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getWording(): ?string
    {
        return $this->wording;
    }

    public function setWording(?string $wording): self
    {
        $this->wording = $wording;

        return $this;
    }

    public function getLoanInFlows(): ?int
    {
        return $this->loanInFlows;
    }

    public function setLoanInFlows(?int $loanInFlows): self
    {
        $this->loanInFlows = ($this->loanBalances ?? 0) + $loanInFlows;
        $this->loanBalances += $loanInFlows;

        return $this;
    }

    public function getLoanOutFlows(): ?int
    {
        return $this->loanOutFlows;
    }

    public function setLoanOutFlows(?int $loanOutFlows): self
    {
        $this->loanOutFlows = $loanOutFlows;
        $this->loanBalances -= $loanOutFlows;

        return $this;
    }

    public function getLoanRenewals(): ?int
    {
        return $this->loanRenewals;
    }

    public function setLoanRenewals(?int $loanRenewals, $addToBalance = false): self
    {
        $this->loanRenewals = $loanRenewals;
        if ($addToBalance) {
            $this->loanBalances += $loanRenewals;
        }

        return $this;
    }

    public function getLoanBalances(): ?int
    {
        return $this->loanBalances;
    }

    public function setLoanBalances(?int $loanBalances): self
    {
        $this->loanBalances = $loanBalances;

        return $this;
    }

    public function getInterests(): ?int
    {
        return $this->interests;
    }

    public function setInterests(?int $interests): self
    {
        $this->interests = $interests;

        return $this;
    }

    public function getObservations(): ?string
    {
        return $this->observations;
    }

    public function setObservations(?string $observations): self
    {
        $this->observations = $observations;

        return $this;
    }

    public function getAccount(): ?Account
    {
        return $this->account;
    }

    public function setAccount(?Account $account): self
    {
        $this->account = $account;
        if ($this->user == null) {
            $this->setUser($account->getUser());
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
        if ($this->account == null) {
            $this->setAccount($user->getAccount());
        }

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

    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function setParent(?self $parent): self
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return Collection|self[]
     */
    public function getChildren(): Collection
    {
        return $this->children;
    }

    public function addChild(self $child): self
    {
        if (!$this->children->contains($child)) {
            $this->children[] = $child;
            $child->setParent($this);
        }

        return $this;
    }

    public function removeChild(self $child): self
    {
        if ($this->children->removeElement($child)) {
            // set the owning side to null (unless already changed)
            if ($child->getParent() === $this) {
                $child->setParent(null);
            }
        }

        return $this;
    }

    public function resolveObservations(): array
    {
        $observations = $this->observations;
        if (!empty($observations)) {
            $observations = json_decode($observations, true) ?? [];
            $observations['observations'] = $observations['observations'] ?? null;
            return $observations;
        }
        return [];
    }

    public function firstObservation(): ?string
    {
        $observations = $this->resolveObservations();
        return !empty($observations) ? ($observations['observations'] ?? '') : '';
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

    public function getAdmin(): ?User
    {
        return $this->admin;
    }

    public function setAdmin(?User $admin): self
    {
        $this->admin = $admin;

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

    public function getRenewalAt(): ?\DateTimeInterface
    {
        return $this->renewalAt;
    }

    public function setRenewalAt(?\DateTimeInterface $renewalAt): self
    {
        $this->renewalAt = $renewalAt;

        return $this;
    }

    public function getPaybackAt(): ?\DateTimeInterface
    {
        return $this->paybackAt;
    }

    public function setPaybackAt(?\DateTimeInterface $paybackAt): self
    {
        $this->paybackAt = $paybackAt;

        return $this;
    }

    /**
     * @return Collection|DebtInterest[]
     */
    public function getDebtInterests(): Collection
    {
        return $this->debtInterests;
    }

    public function addDebtInterest(DebtInterest $debtInterest): self
    {
        if (!$this->debtInterests->contains($debtInterest)) {
            $this->debtInterests[] = $debtInterest;
            $debtInterest->setDebt($this);
        }

        return $this;
    }

    public function removeDebtInterest(DebtInterest $debtInterest): self
    {
        if ($this->debtInterests->removeElement($debtInterest)) {
            // set the owning side to null (unless already changed)
            if ($debtInterest->getDebt() === $this) {
                $debtInterest->setDebt(null);
            }
        }

        return $this;
    }

    public function isDeletable(): bool
    {
        return $this->isCurrent;
        // return $this->parent === null && $this->getChildren()->isEmpty();
    }

    public function isUpdatable(): bool
    {
        return $this->isDeletable();
    }

    public function getPreviousBalances(): ?int
    {
        return $this->previousBalances;
    }

    public function setPreviousBalances(int $previousBalances): self
    {
        $this->previousBalances = $previousBalances;

        return $this;
    }

    /**
     * @return Collection|UserInterest[]
     */
    public function getUserInterests(): Collection
    {
        return $this->userInterests;
    }

    public function addUserInterest(UserInterest $userInterest): self
    {
        if (!$this->userInterests->contains($userInterest)) {
            $this->userInterests[] = $userInterest;
            $userInterest->setDebt($this);
        }

        return $this;
    }

    public function removeUserInterest(UserInterest $userInterest): self
    {
        if ($this->userInterests->removeElement($userInterest)) {
            // set the owning side to null (unless already changed)
            if ($userInterest->getDebt() === $this) {
                $userInterest->setDebt(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|DebtRenewal[]
     */
    public function getDebtRenewals(): Collection
    {
        return $this->debtRenewals;
    }

    public function addDebtRenewal(DebtRenewal $debtRenewal): self
    {
        if (!$this->debtRenewals->contains($debtRenewal)) {
            $this->debtRenewals[] = $debtRenewal;
            $debtRenewal->setDebt($this);
        }

        return $this;
    }

    public function removeDebtRenewal(DebtRenewal $debtRenewal): self
    {
        if ($this->debtRenewals->removeElement($debtRenewal)) {
            // set the owning side to null (unless already changed)
            if ($debtRenewal->getDebt() === $this) {
                $debtRenewal->setDebt(null);
            }
        }

        return $this;
    }

    public function lastRenewal(): DebtRenewal|bool
    {
        return $this->debtRenewals->last();
    }

    public function getRenewalPeriod(): ?\DateInterval
    {
        return $this->renewalPeriod;
    }

    public function setRenewalPeriod(\DateInterval $renewalPeriod): self
    {
        $this->renewalPeriod = $renewalPeriod;

        return $this;
    }

    public function getPreviousTotalInflows(): ?int
    {
        return $this->previousTotalInflows;
    }

    public function setPreviousTotalInflows(int $previousTotalInflows): self
    {
        $this->previousTotalInflows = $previousTotalInflows;

        return $this;
    }

    public function getPreviousTotalOutflows(): ?int
    {
        return $this->previousTotalOutflows;
    }

    public function setPreviousTotalOutflows(int $previousTotalOutflows): self
    {
        $this->previousTotalOutflows = $previousTotalOutflows;

        return $this;
    }

    public function delete(EntityManagerInterface $manager): self
    {
        foreach ($manager->getRepository(UserInterest::class)->findBy(["debt" => $this]) as $interest) {
            $manager->remove($interest);
        }
        foreach ($this->getUserInterests() as $userInterest) {
            $manager->remove($userInterest);
        }
        foreach ($this->getDebtInterests() as $debtInterest) {
            $manager->remove($debtInterest);
        }
        foreach ($this->getAvalistes() as $avaliste) {
            $manager->remove($avaliste);
        }
        $manager->remove($this);
        $this->getAccount()->reinitializeDebt();
        $manager->flush();

        return $this;
    }

    public function getRenewalDate(): ?\DateTimeInterface
    {
        return $this->renewalDate;
    }

    public function setRenewalDate(): self
    {
        if ($this->renewalDate === null and $this->renewalPeriod !== null) {
            $lastDate = $this->renewalAt ?? $this->createdAt;
            $this->renewalDate = (new DateTime($lastDate->format("Y-m-d")))
                ->add($this->renewalPeriod);
        }
        return $this;
    }

    public function getIsRenewable(): bool
    {
        return $this->isRenewable;
    }
    public function setIsRenewable(): self
    {
        $this->isRenewable = new \DateTime() > $this->renewalDate;

        return $this;
    }

    public function getDebtRate(): ?float
    {
        return $this->debtRate;
    }

    public function setDebtRate(?float $debtRate): self
    {
        $this->debtRate = $debtRate;

        return $this;
    }

    public function getLinkedDebtRenewal(): ?DebtRenewal
    {
        return $this->linkedDebtRenewal;
    }

    public function setLinkedDebtRenewal(?DebtRenewal $linkedDebtRenewal): self
    {
        // unset the owning side of the relation if necessary
        if ($linkedDebtRenewal === null && $this->linkedDebtRenewal !== null) {
            $this->linkedDebtRenewal->setLinkedDebt(null);
        }

        // set the owning side of the relation if necessary
        if ($linkedDebtRenewal !== null && $linkedDebtRenewal->getLinkedDebt() !== $this) {
            $linkedDebtRenewal->setLinkedDebt($this);
        }

        $this->linkedDebtRenewal = $linkedDebtRenewal;

        return $this;
    }

    /**
     * @return Collection|DebtAvalist[]
     */
    public function getAvalistes(): Collection
    {
        return $this->avalistes;
    }

    public function addAvaliste(DebtAvalist $avaliste): self
    {
        if (!$this->avalistes->contains($avaliste)) {
            $this->avalistes[] = $avaliste;
            $avaliste->setDebt($this);
        }

        return $this;
    }

    public function removeAvaliste(DebtAvalist $avaliste): self
    {
        if ($this->avalistes->removeElement($avaliste)) {
            // set the owning side to null (unless already changed)
            if ($avaliste->getDebt() === $this) {
                $avaliste->setDebt(null);
            }
        }

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function isInflow(): bool
    {
        return $this->type === Account::INFLOW;
    }

    public function resetLoanInFlows(int $loanInFlows = 0): self
    {
        $this->loanInFlows = $loanInFlows;

        return $this;
    }

    public function resetLoanOutFlows(int $loanOutFlows = 0): self
    {
        $this->loanOutFlows = $loanOutFlows;

        return $this;
    }

    public function isDebtRenewalLinked(): bool
    {
        return $this->getLinkedDebtRenewal() !== null;
    }

    public function isLinked(): bool
    {
        return $this->isDebtRenewalLinked();
    }
}
