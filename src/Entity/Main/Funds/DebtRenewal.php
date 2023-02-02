<?php

namespace App\Entity\Main\Funds;

use App\Entity\Main\Users\User;
use App\Repository\Main\Funds\DebtRenewalRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DebtRenewalRepository::class)
 */
class DebtRenewal
{
    const FUND_SUBSTRACT = 1;
    const DEBT_ADD = 2;
    const ANY_ACTION = 3;
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $wording;

    /**
     * @ORM\Column(type="integer")
     */
    private $amount;

    /**
     * @ORM\ManyToOne(targetEntity=Debt::class, inversedBy="debtRenewals")
     * @ORM\JoinColumn(nullable=false)
     */
    private $debt;

    /**
     * @ORM\ManyToOne(targetEntity=Account::class, inversedBy="debtRenewals")
     * @ORM\JoinColumn(nullable=false)
     */
    private $account;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="string", length=4)
     */
    private $year;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="adminDebtRenewals")
     */
    private $admin;

    public int $renewalOutflow = 1;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $observation;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $debtRate;

    /**
     * @ORM\OneToOne(targetEntity=Fund::class, inversedBy="linkedDebtRenewal", cascade={"persist", "remove"})
     */
    private $linkedFund;

    /**
     * @ORM\OneToOne(targetEntity=Debt::class, inversedBy="linkedDebtRenewal", cascade={"persist", "remove"})
     */
    private $linkedDebt;

    public function __construct()
    {
        $this->setCreatedAt(new \DateTime());
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

    public function getAmount(): ?int
    {
        return $this->amount;
    }

    public function setAmount(int $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getDebt(): ?Debt
    {
        return $this->debt;
    }

    public function setDebt(?Debt $debt): self
    {
        $this->debt = $debt;

        return $this;
    }

    public function getAccount(): ?Account
    {
        return $this->account;
    }

    public function setAccount(?Account $account): self
    {
        $this->account = $account;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;
        $this->year = (int) $this->createdAt->format('Y');

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

    public function getAdmin(): ?User
    {
        return $this->admin;
    }

    public function setAdmin(?User $admin): self
    {
        $this->admin = $admin;

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

    public function getDebtRate(): ?float
    {
        return $this->debtRate;
    }

    public function setDebtRate(?float $debtRate): self
    {
        $this->debtRate = $debtRate;

        return $this;
    }

    public function getLinkedFund(): ?Fund
    {
        return $this->linkedFund;
    }

    public function setLinkedFund(?Fund $linkedFund): self
    {
        $this->linkedFund = $linkedFund;

        return $this;
    }

    public function getLinkedDebt(): ?Debt
    {
        return $this->linkedDebt;
    }

    public function setLinkedDebt(?Debt $linkedDebt): self
    {
        $this->linkedDebt = $linkedDebt;

        return $this;
    }
}
