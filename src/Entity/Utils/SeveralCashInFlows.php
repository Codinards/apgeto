<?php

namespace App\Entity\Utils;

use App\Entity\Main\Funds\Account;
use App\Entity\Main\Users\User;
use DateTime;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class SeveralCashInFlows
{
    private $wording;

    private $cashInFlows = 0;

    private Collection $targets;

    private $observations;

    private $createdAt;

    private $admin;

    private $year;

    public function __construct()
    {
        $this->targets = new ArrayCollection();
        $this->setCreatedAt(new DateTime());
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
     * Get the value of cashInFlows
     */
    public function getCashInFlows(): int
    {
        return $this->cashInFlows;
    }

    /**
     * Set the value of cashInFlows
     *
     * @return  self
     */
    public function setCashInFlows(?int $cashInFlows)
    {
        $this->cashInFlows = $cashInFlows;

        return $this;
    }

    /**
     * Get the value of targets
     */
    public function getTargets(): Collection
    {
        return $this->targets;
    }

    /**
     * Set the value of targets
     *
     * @return  self
     */
    public function setTargets(Collection $targets)
    {
        $this->targets = $targets;

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

    /**
     * Get the value of count
     */
    public function getCount()
    {
        return $this->count;
    }
}
