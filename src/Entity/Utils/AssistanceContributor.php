<?php

namespace App\Entity\Utils;

use App\Entity\Assistances\AssistanceType;
use App\Entity\Main\Funds\Account;
use App\Entity\Main\Users\User;

class AssistanceContributor
{
    private int $id;

    private ?string $name = null;

    private ?int $amount = null;

    private $member;

    private Account $account;

    private AssistanceType $type;

    public function hydrate(User $user, AssistanceType $type): self
    {
        $this->id = $user->getId();
        $this->name = $user->getName();
        $this->account = $user->getaccount();
        $this->type = $type;
        return $this;
    }

    /**
     * Get the value of id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the value of id
     *
     * @return  self
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the value of name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the value of name
     *
     * @return  self
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get the value of amount
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set the value of amount
     *
     * @return  self
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get the value of account
     */
    public function getAccount()
    {
        return $this->account;
    }

    /**
     * Set the value of account
     *
     * @return  self
     */
    public function setAccount($account)
    {
        $this->account = $account;

        return $this;
    }

    /**
     * Get the value of type
     */
    public function getType(): AssistanceType
    {
        return $this->type;
    }

    /**
     * Set the value of type
     *
     * @return  self
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
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
    public function setMember($member)
    {
        $this->member = $member;

        return $this;
    }
}
