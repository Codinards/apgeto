<?php

namespace App\Entity\Utils;

use App\Entity\Main\Funds\Account;
use App\Entity\Main\Funds\DebtAvalist;
use App\Entity\Main\Users\User;
use App\Form\Validators\LowerThan;
use App\Tools\AppConstants;
use DateTime;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class LoanInFlows
{
    private $wording;

    private $loanInFlows = 0;

    private $observations;

    private $createdAt;

    private ?int $interests = null;

    private ?Account $account = null;

    private ?User $user = null;

    private ?User $admin = null;

    private ?int $year = null;

    private \DateInterval $renewalPeriod;

    private Collection $avalistes;

    public function __construct()
    {
        $this->createdAt = new DateTime();
        $this->renewalPeriod = new \DateInterval(AppConstants::$RENEWALPERIOD);
        $this->avalistes = new ArrayCollection();
    }

    public function addAvaliste(DebtAvalist $debtAvalist): self
    {
        $this->avalistes->add($debtAvalist);
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

    /**
     * Get the value of firstAvaliste
     */
    public function getFirstAvaliste() //: null|User|Account
    {
        return $this->firstAvaliste;
    }

    /**
     * Set the value of firstAvaliste
     *
     * @return  self
     */
    public function setFirstAvaliste(/*null|User|Account*/$firstAvaliste): self
    {
        $this->firstAvaliste = (!$firstAvaliste instanceof User) ? $firstAvaliste->getUser() : $firstAvaliste;

        return $this;
    }

    /**
     * Get the value of secondAvaliste
     */
    public function getSecondAvaliste() //: null|User|Account
    {
        return $this->secondAvaliste;
    }

    /**
     * Set the value of secondAvaliste
     *
     * @return  self
     */
    public function setSecondAvaliste(/*null|User|Account*/$secondAvaliste): self
    {
        $this->secondAvaliste = (!$secondAvaliste instanceof User) ? $secondAvaliste->getUser() : $secondAvaliste;;

        return $this;
    }

    /**
     * Get the value of thirdAvaliste
     */
    public function getThirdAvaliste() //: null|User|Account
    {
        return $this->thirdAvaliste;
    }

    /**
     * Set the value of thirdAvaliste
     *
     * @return  self
     */
    public function setThirdAvaliste(/*null|User|Account*/$thirdAvaliste): self
    {
        $this->thirdAvaliste = (!$thirdAvaliste instanceof User) ? $thirdAvaliste->getUser() : $thirdAvaliste;

        return $this;
    }

    /**
     * Get the value of fourthAvaliste
     */
    public function getFourthAvaliste() //: null|User|Account
    {
        return $this->fourthAvaliste;
    }

    /**
     * Set the value of fourthAvaliste
     *
     * @return  self
     */
    public function setFourthAvaliste(/*null|User|Account*/$fourthAvaliste): self
    {
        $this->fourthAvaliste = (!$fourthAvaliste instanceof User) ? $fourthAvaliste->getUser() : $fourthAvaliste;

        return $this;
    }

    /**
     * Get the value of fifthAvaliste
     */
    public function getFifthAvaliste() //: null|User|Account
    {
        return $this->fifthAvaliste;
    }

    /**
     * Set the value of fifthAvaliste
     *
     * @return  self
     */
    public function setFifthAvaliste(/*null|User|Account*/$fifthAvaliste): self
    {
        $this->fifthAvaliste = (!$fifthAvaliste instanceof User) ? $fifthAvaliste->getUser() : $fifthAvaliste;

        return $this;
    }

    /**
     * Get the value of firstObservation
     */
    public function getFirstObservation(): ?string
    {
        return $this->firstObservation;
    }

    /**
     * Set the value of firstObservation
     *
     * @return  self
     */
    public function setFirstObservation(?string $firstObservation): self
    {
        $this->firstObservation = $firstObservation;

        return $this;
    }

    /**
     * Get the value of secondObservation
     */
    public function getSecondObservation(): ?string
    {
        return $this->secondObservation;
    }

    /**
     * Set the value of secondObservation
     *
     * @return  self
     */
    public function setSecondObservation(?string $secondObservation): self
    {
        $this->secondObservation = $secondObservation;

        return $this;
    }

    /**
     * Get the value of thirdObservation
     */
    public function getThirdObservation(): ?string
    {
        return $this->thirdObservation;
    }

    /**
     * Set the value of thirdObservation
     *
     * @return  self
     */
    public function setThirdObservation(?string $thirdObservation): self
    {
        $this->thirdObservation = $thirdObservation;

        return $this;
    }

    /**
     * Get the value of fourthObservation
     */
    public function getFourthObservation(): ?string
    {
        return $this->fourthObservation;
    }

    /**
     * Set the value of fourthObservation
     *
     * @return  self
     */
    public function setFourthObservation(?string $fourthObservation): self
    {
        $this->fourthObservation = $fourthObservation;

        return $this;
    }

    /**
     * Get the value of fifthObservation
     */
    public function getFifthObservation(): ?string
    {
        return $this->fifthObservation;
    }

    /**
     * Set the value of fifthObservation
     *
     * @return  self
     */
    public function setFifthObservation(?string $fifthObservation): self
    {
        $this->fifthObservation = $fifthObservation;

        return $this;
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
        //$this->setSolde($account->getLoanBalances());
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

    public function getRenewalPeriod(): \DateInterval
    {
        return $this->renewalPeriod;
    }

    public function setRenewalPeriod(\DateInterval $dateInterval): self
    {
        $this->renewalPeriod = $dateInterval;

        return $this;
    }

    /**
     * Get the value of avalistes
     * 
     * @return Collection|DebtAvalist[]
     */
    public function getAvalistes(): Collection|array
    {
        return $this->avalistes;
    }
}
