<?php

namespace App\Entity\Main\Funds;

use App\Entity\EntityTraits\GlobalMethodsTrait;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use App\Repository\Main\Funds\DebtInterestRepository;
use App\Tools\AppConstants;
use DateTimeInterface;

/**
 * @ORM\Entity(repositoryClass=DebtInterestRepository::class)
 * @ORM\Table("debt_interest")
 * @ORM\HasLifecycleCallbacks()
 */
class DebtInterest
{
    use GlobalMethodsTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private ?string $wording;

    /**
     * @ORM\Column(type="integer")
     */
    private ?int $interest;

    /**
     * @ORM\Column(type="boolean", options={"default":false})
     */
    private bool $isRenewal = false;

    /**
     * @ORM\Column(type="datetime")
     */
    private ?DateTimeInterface $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity=Debt::class, inversedBy="debtInterests")
     * @ORM\JoinColumn(nullable=false)
     */
    private ?Debt $debt;

    /**
     * @var int|string
     * @ORM\Column(type="string", length=4)
     */
    private int|string $year;

    /**
     * @var integer
     */
    private int $accountInterest = 0;

    /**
     * @var integer
     */
    private int $debtInterest = 0;

    /**
     * @var integer
     */
    private int $savingInterest = 0;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $debtRate;

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

    public function getInterest(): ?int
    {
        return $this->interest;
    }

    public function setInterest(int $interest): self
    {
        $this->interest = $interest;

        $this->accountInterest = (int) ($interest * AppConstants::$INTEREST_COMMON_PERCENT);
        $this->debtInterest = (int) ($interest * AppConstants::$INTEREST_BORROWER_PERCENT);
        $this->savingInterest = $interest - ($this->accountInterest + $this->debtInterest);

        return $this;
    }

    public function getIsRenewal(): ?bool
    {
        return $this->isRenewal;
    }

    public function setIsRenewal(bool $isRenewal): self
    {
        $this->isRenewal = $isRenewal;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;
        $this->setYear($createdAt->format('Y'));

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

    /**
     * Get the value of year
     *
     * @return  int|string
     */
    public function getYear(): int|string
    {
        return (int) $this->year;
    }

    /**
     * Set the value of year
     *
     * @param  int|string  $year
     *
     * @return  self
     */
    public function setYear(int|string $year)
    {
        $this->year = (int) $year;

        return $this;
    }

    /**
     * Get the value of accountInterest
     */
    public function getAccountInterest(): int
    {
        return $this->accountInterest;
    }

    /**
     * Get the value of debtInterest
     */
    public function getDebtInterest(): int
    {
        return $this->debtInterest;
    }

    /**
     * Get the value of savingInterest
     */
    public function getSavingInterest(): int
    {
        return $this->savingInterest;
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
}
