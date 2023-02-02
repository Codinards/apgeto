<?php

namespace App\Entity\Utils;

use App\Entity\Assistances\AssistanceType;
use App\Entity\Assistances\Contributor;

class AssistanceFilter
{
    private ?AssistanceType $type = null;

    private ?int $member = null;

    private ?Contributor $contributor = null;

    private $period = null;

    private $toArray = [];

    /**
     * Get the value of type
     */
    public function getType(): ?AssistanceType
    {
        return $this->type;
    }

    /**
     * Set the value of type
     *
     * @return  self
     */
    public function setType(?AssistanceType $type)
    {
        $this->type = $type;
        if ($type) {
            $this->toArray['type'] = $type->getId();
        }

        return $this;
    }

    /**
     * Get the value of contributor
     */
    public function getContributor(): ?Contributor
    {
        return $this->contributor;
    }

    /**
     * Set the value of contributor
     *
     * @return  self
     */
    public function setContributor(?Contributor $contributor)
    {
        $this->contributor = $contributor;
        if ($contributor) {
            $this->toArray['contributor'] = $contributor;
        }

        return $this;
    }

    /**
     * Get the value of period
     */
    public function getPeriod(): null|string|int
    {
        return $this->period;
    }

    /**
     * Set the value of period
     *
     * @return  self
     */
    public function setPeriod(null|string|int $period)
    {
        $this->period = $period;

        if ($period) {
            $this->toArray['year'] = $period;
        }

        return $this;
    }

    public function toArray(): array
    {
        return $this->toArray;
    }

    /**
     * Get the value of member
     */
    public function getMember()
    {
        return $this->member;
    }

    /**
     * Set the value of member
     *
     * @return  self
     */
    public function setMember(?int $member)
    {
        $this->member = $member;
        if ($member) {
            $this->toArray['user'] = $member;
        }

        return $this;
    }
}
