<?php

namespace App\Entity\Interests;

use App\Entity\Main\Funds\Debt;
use App\Entity\Main\Users\User;
use App\Repository\Interests\UserInterestRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=UserInterestRepository::class)
 * @ORM\Table("user_interest")
 */
class UserInterest
{
    static int $TOTAL_ACCOUNT_INTEREST = 0;
    static int $TOTAL_DEBT_INTEREST = 0;
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="userInterests")
     * @ORM\JoinColumn(nullable=false)
     */
    private ?User $user = null;

    /**
     * @ORM\Column(type="integer", name="account_balance")
     */
    private int $accountBalance = 0;

    /**
     * @ORM\Column(type="integer", name="debt_balance")
     */
    private int $debtBalance = 0;

    /**
     * @ORM\Column(type="integer", name="account_interest")
     */
    private int $accountInterest = 0;

    /**
     * @ORM\Column(type="integer", name="debt_interest")
     */
    private int $debtInterest = 0;

    /**
     * @ORM\Column(type="string", length=2)
     */
    private ?string $month = null;

    /**
     * @ORM\Column(type="string", length=4)
     */
    private ?string $year = null;

    /**
     * @ORM\ManyToOne(targetEntity=Debt::class, inversedBy="userInterests")
     * @ORM\JoinColumn(nullable=false)
     */
    private Debt $debt;


    public function getId(): ?int
    {
        return $this->id;
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

    public function getAccountBalance(): ?int
    {
        return $this->accountBalance;
    }

    public function setAccountBalance(int $accountBalance): self
    {
        $this->accountBalance = $accountBalance;

        return $this;
    }

    public function getDebtBalance(): ?int
    {
        return $this->debtBalance;
    }

    public function setDebtBalance(int $debtBalance): self
    {
        $this->debtBalance = $debtBalance;

        return $this;
    }

    public function getAccountInterest(): ?int
    {
        return $this->accountInterest;
    }

    public function setAccountInterest(int $accountInterest): self
    {
        $accountInterest = $accountInterest < 10 ? 0 : $accountInterest;
        $tenth = (int) substr($accountInterest, -2);

        if (!in_array($tenth, [0, 25, 50, 75])) {
            switch ($tenth) {
                case $tenth < 25:
                    $accountInterest -= $tenth;
                    break;
                case $tenth > 25 and $tenth < 50:
                    $accountInterest -= $tenth + 25;
                    break;
                case $tenth > 50 and $tenth < 75:
                    $accountInterest -= $tenth + 50;
                    break;
                default:
                    $accountInterest -= $tenth + 75;
                    break;
            }
        }

        $this->accountInterest = $accountInterest;

        self::$TOTAL_ACCOUNT_INTEREST += $accountInterest;

        return $this;
    }

    public function getDebtInterest(): ?int
    {
        return $this->debtInterest;
    }

    public function setDebtInterest(int $debtInterest): self
    {
        $debtInterest = $debtInterest < 10 ? 0 : $debtInterest;
        $tenth = (int) substr($debtInterest, -2);

        if (!in_array($tenth, [0, 25, 50, 75])) {
            switch ($tenth) {
                case $tenth < 25:
                    $debtInterest -= $tenth;
                    break;
                case $tenth > 25 and $tenth < 50:
                    $debtInterest -= $tenth + 25;
                    break;
                case $tenth > 50 and $tenth < 75:
                    $debtInterest -= $tenth + 50;
                    break;
                default:
                    $debtInterest -= $tenth + 75;
                    break;
            }
        }

        $this->debtInterest = $debtInterest;
        self::$TOTAL_DEBT_INTEREST += $debtInterest;

        return $this;
    }

    public function getMonth(): ?string
    {
        return $this->month;
    }

    public function setMonth(string $month): self
    {
        $this->month = $month;

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

    public function getDebt(): ?Debt
    {
        return $this->debt;
    }

    public function setDebt(?Debt $debt): self
    {
        $this->debt = $debt;

        return $this;
    }
}
