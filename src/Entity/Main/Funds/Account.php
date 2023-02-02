<?php

namespace App\Entity\Main\Funds;

use App\Entity\Main\Users\User;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use App\Tools\Entity\FlowTransactionValidator;
use App\Entity\EntityTraits\GlobalMethodsTrait;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use App\Repository\Main\Funds\AccountRepository;
use Doctrine\Common\Collections\ArrayCollection;
use App\Form\Extensions\Validations\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=AccountRepository::class)
 * @ORM\HasLifecycleCallbacks()
 * @UniqueEntity("user")
 * @ORM\Table("account")
 */
class Account
{
    const INFLOW = 'inflow';
    const OUTFLOW = 'outflow';
    use GlobalMethodsTrait;
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     *
     */
    private $id;

    /**
     * @ORM\Column(type="integer", name="cash_in_flows", options={"default":0})
     *
     */
    private $cashInFlows = 0;

    /**
     * @ORM\Column(type="integer", name="cash_out_flows", options={"default":0})
     *
     */
    private $cashOutFlows = 0;

    /**
     * @ORM\Column(type="integer", name="cash_balances", options={"default":0})
     *
     */
    private $cashBalances = 0;

    /**
     * @ORM\Column(type="integer", name="loan_in_flows", options={"default":0})
     *
     */
    private $loanInFlows = 0;

    /**
     * @ORM\Column(type="integer", name="loan_out_flows", options={"default":0})
     *
     */
    private $loanOutFlows = 0;

    /**
     * @ORM\Column(type="integer", name="loan_balances", options={"default":0})
     *
     */
    private $loanBalances = 0;

    /**
     * @ORM\OneToOne(targetEntity=User::class, inversedBy="account", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     *
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="adminAccounts")
     * @ORM\JoinColumn(nullable=true)
     */
    private $admin;

    /**
     * @ORM\OneToMany(targetEntity=Fund::class, mappedBy="account", orphanRemoval=true)
     */
    private $funds;

    /**
     * @ORM\Column(type="datetime")
     *
     */
    private $createdAt;

    /**
     * @ORM\OneToMany(targetEntity=Debt::class, mappedBy="account", orphanRemoval=true)
     */
    private $debts;

    /**
     * @ORM\OneToOne(targetEntity=Debt::class, cascade={"persist", "remove"})
     */
    private $currentDebt;

    /**
     * @ORM\OneToMany(targetEntity=DebtRenewal::class, mappedBy="account")
     */
    private $debtRenewals;

    public function __construct()
    {
        $this->funds = new ArrayCollection();
        $this->debts = new ArrayCollection();
        $this->debtRenewals = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCashInFlows(): ?int
    {
        return $this->cashInFlows;
    }

    public function setCashInFlows(int $cashInFlows): self
    {
        $this->cashInFlows += $cashInFlows;
        $this->cashBalances += $cashInFlows;

        return $this;
    }

    public function resetCashInFlows(int $cashInFlows = 0): self
    {
        $this->cashInFlows = $cashInFlows;

        return $this;
    }

    public function getCashOutFlows(): ?int
    {
        return $this->cashOutFlows;
    }

    public function setCashOutFlows(int $cashOutFlows): self
    {
        $this->cashOutFlows += $cashOutFlows;
        $this->cashBalances -= $cashOutFlows;

        return $this;
    }


    public function resetCashOutFlows(int $cashOutFlows = 0): self
    {
        $this->cashOutFlows = $cashOutFlows;

        return $this;
    }

    public function getCashBalances(): ?int
    {
        return $this->cashBalances;
    }

    public function setCashBalances(int $cashBalances): self
    {
        $this->cashBalances = $cashBalances;

        return $this;
    }

    public function getLoanInFlows(): ?int
    {
        return $this->loanInFlows;
    }

    public function setLoanInFlows(int $loanInFlows): self
    {
        $this->loanInFlows += $loanInFlows;
        $this->loanBalances += $loanInFlows;

        return $this;
    }

    public function resetLoanInFlows(int $loanInFlows = 0): self
    {
        $this->loanInFlows = $loanInFlows;

        return $this;
    }

    public function getLoanOutFlows(): ?int
    {
        return $this->loanOutFlows;
    }

    public function setLoanOutFlows(int $loanOutFlows): self
    {
        $this->loanOutFlows += $loanOutFlows;
        $this->loanBalances -= $loanOutFlows;

        return $this;
    }

    public function resetLoanOutFlows(int $loanOutFlows = 0): self
    {
        $this->loanOutFlows = $loanOutFlows;

        return $this;
    }

    public function getLoanBalances(): ?int
    {
        return $this->loanBalances;
    }

    public function setLoanBalances(int $loanBalances): self
    {
        $this->loanBalances = $loanBalances;

        return $this;
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

    /**
     * @return Collection|Fund[]
     */
    public function getFunds(): Collection
    {
        return $this->funds;
    }

    public function addFund(Fund $fund): self
    {
        if (!$this->funds->contains($fund)) {
            $this->funds[] = $fund;
            $fund->setAccount($this);
        }

        return $this;
    }

    public function removeFund(Fund $fund): self
    {
        if ($this->funds->removeElement($fund)) {
            // set the owning side to null (unless already changed)
            if ($fund->getAccount() === $this) {
                $fund->setAccount(null);
            }
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

    /**
     * @return Collection|Debt[]
     */
    public function getDebts(): Collection
    {
        return $this->debts;
    }

    public function addDebt(Debt $debt): self
    {
        if (!$this->debts->contains($debt)) {
            $this->debts[] = $debt;
            $debt->setAccount($this);
        }

        return $this;
    }

    public function removeDebt(Debt $debt): self
    {
        if ($this->debts->removeElement($debt)) {
            // set the owning side to null (unless already changed)
            if ($debt->getAccount() === $this) {
                $debt->setAccount(null);
            }
        }

        return $this;
    }

    public function canLoan(bool $throwException = true): bool
    {
        return FlowTransactionValidator::canLoan($this, $throwException);
    }

    public function hasLoan(): bool
    {
        return $this->loanBalances !== 0;
    }

    public function __toString()
    {
        return $this->user->getName();
    }

    public function getCurrentDebt(): ?Debt
    {
        return $this->currentDebt;
    }

    public function setCurrentDebt(?Debt $currentDebt): self
    {
        $this->currentDebt = $currentDebt;

        return $this;
    }

    public function reinitializeDebt()
    {
        $this->setLoanBalances(0);
        $this->resetLoanInFlows(0);
        $this->resetLoanOutFlows(0);
        $this->setCurrentDebt(null);
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
            $debtRenewal->setAccount($this);
        }

        return $this;
    }

    public function removeDebtRenewal(DebtRenewal $debtRenewal): self
    {
        if ($this->debtRenewals->removeElement($debtRenewal)) {
            // set the owning side to null (unless already changed)
            if ($debtRenewal->getAccount() === $this) {
                $debtRenewal->setAccount(null);
            }
        }

        return $this;
    }
}
