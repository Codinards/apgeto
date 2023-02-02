<?php

namespace App\Entity\Utils;

use App\Entity\Main\Funds\Account;
use App\Entity\Main\Users\User;
use DateTime;
use DateTimeInterface;

class CashOutFlows
{
    private $wording;

    private $cashOutFlows = 0;

    private $account;

    private $user;

    private $observations;

    private $createdAt;

    private $admin;

    private $year;

    public function __construct()
    {
        $this->createdAt = new DateTime();
    }

    /**
     * Get the value of wording
     */
    public function getWording(): string
    {
        return $this->wording;
    }

    /**
     * Set the value of wording
     *
     * @return  self
     */
    public function setWording(?string $wording)
    {
        $this->wording = $wording;

        return $this;
    }

    /**
     * Get the value of cashOutFlows
     */
    public function getCashOutFlows(): int
    {
        return $this->cashOutFlows;
    }

    /**
     * Set the value of cashOutFlows
     *
     * @return  self
     */
    public function setCashOutFlows(?int $cashOutFlows)
    {
        $this->cashOutFlows = $cashOutFlows;

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

        return $this;
    }

    /**
     * Get the value of user
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * Set the value of user
     *
     * @return  self
     */
    public function setUser(?User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get the value of observations
     */
    public function getObservations(): ?string
    {
        return $this->observations;
    }

    /**
     * Set the value of observations
     *
     * @return  self
     */
    public function setObservations(?string $observation)
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
    public function setCreatedAt(?DateTimeInterface $createdAt)
    {
        $this->createdAt = $createdAt;

        $this->year = (int) $this->createdAt->format('Y');

        return $this;
    }

    /**
     * Get the value of admin
     */
    public function getAdmin(): ?User
    {
        return $this->admin;
    }

    /**
     * Set the value of admin
     *
     * @return  self
     */
    public function setAdmin(?User $admin)
    {
        $this->admin = $admin;

        return $this;
    }

    /**
     * Get the value of year
     */
    public function getYear()
    {
        return $this->year;
    }
}
