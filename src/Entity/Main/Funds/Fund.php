<?php

namespace App\Entity\Main\Funds;

use App\Entity\Assistances\Contributor;
use App\Entity\Main\Operations\Operation;
use App\Entity\Main\Users\User;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\EntityTraits\GlobalMethodsTrait;
use App\Repository\Main\Funds\FundRepository;
use DateTimeInterface;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;

/**
 * @ORM\Entity(repositoryClass=FundRepository::class)
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table("fund")
 */
class Fund
{
    use GlobalMethodsTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * 
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="string", length=255)
     * 
     */
    private ?string $wording = null;

    /**
     * @ORM\Column(type="integer", name="cash_in_flows", nullable=true)
     * 
     */
    private ?int $cashInFlows = null;

    /**
     * @ORM\Column(type="integer", name="cash_out_flows", nullable=true)
     * 
     */
    private ?int $cashOutFlows = null;

    /**
     * @ORM\Column(type="integer", name="cash_balances", options={"default":0})
     * 
     */
    private int $cashBalances = 0;

    /**
     * @ORM\ManyToOne(targetEntity=Account::class, inversedBy="funds")
     * @ORM\JoinColumn(nullable=false)
     * 
     */
    private ?Account $account = null;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="funds")
     * @ORM\JoinColumn(nullable=false)
     * 
     */
    private ?User $user = null;

    /**
     * @ORM\ManyToOne(targetEntity=\App\Entity\Main\users\User::class, inversedBy="adminFunds")
     * @ORM\JoinColumn(nullable=false)
     * 
     */
    private ?User $admin = null;

    /**
     * @ORM\Column(type="datetime")
     * 
     */
    private ?DateTimeInterface $createdAt = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * 
     */
    private ?string $observations = null;

    /**
     * @ORM\Column(type="string", length=4)
     * 
     */
    private null|int|string $year = null;

    /**
     * @ORM\Column(type="integer")
     */
    private int $previousBalances = 0;

    /**
     * @ORM\Column(type="integer", options={"default":0})
     */
    private int $previousTotalInflows = 0;

    /**
     * @ORM\Column(type="integer", options={"default":0})
     */
    private int $previousTotalOutflows = 0;

    /**
     * @ORM\OneToOne(targetEntity=Operation::class, inversedBy="fund", cascade={"persist", "remove"})
     */
    private ?Operation $operation = null;

    /**
     * @ORM\OneToOne(targetEntity=Contributor::class, inversedBy="fund", cascade={"persist", "remove"})
     */
    private ?Contributor $assistance = null;

    /**
     * @ORM\OneToOne(targetEntity=DebtRenewal::class, mappedBy="linkedFund", cascade={"persist", "remove"})
     */
    private $linkedDebtRenewal;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private $type;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getCashInFlows(): ?int
    {
        return $this->cashInFlows;
    }

    public function setCashInFlows(?int $cashInFlows): self
    {
        $this->cashInFlows = $cashInFlows;
        $this->cashBalances += $cashInFlows;

        return $this;
    }

    public function getCashOutFlows(): ?int
    {
        return $this->cashOutFlows;
    }

    public function setCashOutFlows(?int $cashOutFlows): self
    {
        $this->cashOutFlows = $cashOutFlows;
        $this->cashBalances -= $cashOutFlows;

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

    public function getAdmin(): ?\App\Entity\Main\users\User
    {
        return $this->admin;
    }

    public function setAdmin(?\App\Entity\Main\users\User $admin): self
    {
        $this->admin = $admin;

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

    public function getObservations(): ?string
    {
        return $this->observations;
    }

    public function setObservations(?string $observations): self
    {
        $this->observations = $observations;

        return $this;
    }

    public function getYear(): ?string
    {
        return $this->year;
    }

    public function setYear(string $year): self
    {
        $this->year = $year;

        return $this;
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

    public function getOperation(): ?Operation
    {
        return $this->operation;
    }

    public function setOperation(?Operation $operation): self
    {
        $this->operation = $operation;

        return $this;
    }

    public function getAssistance(): ?Contributor
    {
        return $this->assistance;
    }

    public function setAssistance(?Contributor $assistance): self
    {
        $this->assistance = $assistance;

        return $this;
    }

    public function isOperationLinked(): bool
    {
        return $this->getOperation() !== null;
    }

    public function isAssistanceLinked(): bool
    {
        return $this->getAssistance() !== null;
    }

    public function isDebtRenewalLinked(): bool
    {
        return $this->getLinkedDebtRenewal() !== null;
    }

    public function isLinked(): bool
    {
        return $this->isOperationLinked()
            || $this->isAssistanceLinked()
            || $this->isDebtRenewalLinked();
    }

    public function getLinkedDebtRenewal(): ?DebtRenewal
    {
        return $this->linkedDebtRenewal;
    }

    public function setLinkedDebtRenewal(?DebtRenewal $linkedDebtRenewal): self
    {
        // unset the owning side of the relation if necessary
        if ($linkedDebtRenewal === null && $this->linkedDebtRenewal !== null) {
            $this->linkedDebtRenewal->setLinkedFund(null);
        }

        // set the owning side of the relation if necessary
        if ($linkedDebtRenewal !== null && $linkedDebtRenewal->getLinkedFund() !== $this) {
            $linkedDebtRenewal->setLinkedFund($this);
        }

        $this->linkedDebtRenewal = $linkedDebtRenewal;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function isInflow(): bool
    {
        return $this->type === Account::INFLOW;
    }
}
