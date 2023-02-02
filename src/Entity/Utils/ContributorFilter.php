<?php

namespace App\Entity\Utils;

class ContributorFilter
{
    private $minCashBalance;
    private $maxCashBalance;
    private $minCashBetween;
    private $maxCashBetween;


    /**
     * Get the value of minCashBalance
     */
    public function getMinCashBalance()
    {
        return $this->minCashBalance;
    }

    /**
     * Set the value of minCashBalance
     *
     * @return  self
     */
    public function setMinCashBalance(?int $minCashBalance)
    {
        $this->minCashBalance = $minCashBalance;

        return $this;
    }

    /**
     * Get the value of maxCashBalance
     */
    public function getMaxCashBalance()
    {
        return $this->maxCashBalance;
    }

    /**
     * Set the value of maxCashBalance
     *
     * @return  self
     */
    public function setMaxCashBalance(?int $maxCashBalance)
    {
        $this->maxCashBalance = $maxCashBalance;

        return $this;
    }

    /**
     * Get the value of minCashBetween
     */
    public function getMinCashBetween()
    {
        return $this->minCashBetween;
    }

    /**
     * Set the value of minCashBetween
     *
     * @return  self
     */
    public function setMinCashBetween(?int $minCashBetween)
    {
        $this->minCashBetween = $minCashBetween;

        return $this;
    }

    /**
     * Get the value of maxCashBetwee
     */
    public function getMaxCashBetween()
    {
        return $this->maxCashBetween;
    }

    /**
     * Set the value of maxCashBetwee
     *
     * @return  self
     */
    public function setMaxCashBetween(?int $maxCashBetween)
    {
        $this->maxCashBetween = $maxCashBetween;

        return $this;
    }
}
