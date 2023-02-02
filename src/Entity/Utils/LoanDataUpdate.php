<?php

namespace App\Entity\Utils;

use App\Entity\Main\Funds\Account;
use App\Entity\Main\Funds\Debt;
use App\Entity\Main\Funds\DebtAvalist;
use App\Entity\Main\Users\User;
use App\Tools\AppConstants;
use DateTime;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class LoanDataUpdate
{
    private string $wording;

    private int $loanInFlows = 0;

    private int $loanOutFlows = 0;

    private int $loanBalances = 0;

    private bool $isInflow = true;

    private ?string $observations = null;

    private ?DateTimeInterface $createdAt;

    private ?DateTimeInterface $paybackAt;

    private Collection $avalistes;

    private ?int $interests = null;

    private ?Account $account = null;

    private ?User $user = null;

    private ?User $admin = null;

    private ?int $year = null;

    private $renewalPeriod;

    private Debt $debt;

    public function __construct()
    {
        $this->avalistes = new ArrayCollection();
    }

    public function hydrate(Debt $debt): self
    {
        $this->debt = $debt;
        $this->wording = $debt->getWording();
        $this->loanBalances = $debt->getloanBalances();
        $this->createdAt = $debt->getCreatedAt();
        if ($this->isInflow) {
            $this->loanInFlows = (int) $debt->getLoanInFlows();
            $this->paybackAt = $debt->getPaybackAt();
            $this->interests = $debt->getInterests();
            $this->renewalPeriod = $debt->getRenewalPeriod();
            $this->avalistes = $debt->getAvalistes();
        } else {
            $this->loanOutFlows = (int) $debt->getloanOutFlows();
        }
        $this->account = $debt->getAccount();
        $this->user = $debt->getUser();
        $this->year = $debt->getYear();
        $this->observations = $debt->getObservations();

        return $this;
    }

    /**
     * Get the value of wording
     */
    public function getWording(): ?string
    {
        return $this->wording;
    }

    /**
     * Set the value of wording
     *
     * @return  self
     */
    public function setWording(?string $wording): self
    {
        $this->wording = $wording;

        return $this;
    }

    /**
     * Get the value of loanInFlows
     */
    public function getLoanInFlows(): ?int
    {
        return $this->loanInFlows;
    }

    /**
     * Set the value of loanInFlows
     *
     * @return  self
     */
    public function setLoanInFlows(?int $loanInFlows): self
    {
        $this->loanInFlows = $loanInFlows;

        return $this;
    }

    /**
     * Get the value of observation
     */
    public function getObservations(): ?string
    {
        return $this->observations;
    }

    /**
     * Set the value of observation
     *
     * @return  self
     */
    public function setObservations(?string $observation): self
    {
        $this->observations = $observation;

        return $this;
    }

    /**
     * Get the value of createdAt
     */
    public function getCreatedAt(): ?DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * Set the value of createdAt
     *
     * @return  self
     */
    public function setCreatedAt(?DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        $this->year = (int) $this->createdAt->format('Y');

        return $this;
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


    public function getAvalistes(): Collection
    {
        return $this->avalistes;
    }

    /**
     * Get the value of account
     */
    public function getAccount(): ?Account
    {
        return $this->account;
    }

    /**
     * Set the value of account
     *
     * @return  self
     */
    public function setAccount(?Account $account)
    {
        $this->account = $account;

        return $this;
    }

    /**
     * Get the value of user
     */
    public function getUser() //: null|User|Account
    {
        return $this->user;
    }

    /**
     * Set the value of user
     *
     * @return  self
     */
    public function setUser(/*null|User|Account*/$user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get the value of admin
     */
    public function getAdmin(): User
    {
        return $this->admin;
    }

    /**
     * Set the value of admin
     *
     * @return  self
     */
    public function setAdmin(User $admin)
    {
        $this->admin = $admin;

        return $this;
    }

    /**
     * Get the value of year
     */
    public function getYear(): ?int
    {
        return $this->year;
    }


    /**
     * Get the value of year
     */
    public function setYear(?int $year): self
    {
        $this->year = $year;

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

    public function resolveInterest(): self
    {
        if (is_null($this->interests)) {
            $this->interests = (int) $this->loanInFlows * AppConstants::$INTEREST_RATE;
        }
        return $this;
    }

    /**
     * Get the value of paybackAt
     */
    public function getPaybackAt(): ?DateTimeInterface
    {
        return $this->paybackAt;
    }

    /**
     * Set the value of paybackAt
     *
     * @return  self
     */
    public function setPaybackAt(?DateTimeInterface $paybackAt)
    {
        $this->paybackAt = $paybackAt;

        return $this;
    }

    public function getLoanOutFlows(): int
    {
        return $this->loanOutFlows;
    }

    public function setLoanOutFlows(int $loanOutFlows): self
    {
        $this->loanOutFlows = $loanOutFlows;

        return $this;
    }

    public function getLoanBalances(): int
    {
        return $this->loanBalances;
    }

    public function setLoanBalances(int $loanBalances): self
    {
        $this->loanBalances = $loanBalances;

        return $this;
    }

    public function getIsInflow(): bool
    {
        return $this->isInflow;
    }

    public function setIsInflow(bool $isInflow): self
    {
        $this->isInflow = $isInflow;

        return $this;
    }

    public function getDebt(): Debt
    {
        return $this->debt;
    }
}
