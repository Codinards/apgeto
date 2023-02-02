<?php

namespace App\Entity\Utils;

use App\Entity\Main\Operations\Type;
use App\Entity\Main\Users\User;
use DateTime;
use Doctrine\Common\Collections\Collection;

class OperationFromAccount
{
    /**
     * @var string
     */
    private $wording;

    /**
     * @var int
     */
    private $inflows;
    /**
     * @var int
     */
    private $outflows;

    /**
     * @var int
     */
    private $balance = 0;

    /**
     * @var string
     */
    private $observation;

    /**
     * @var DateTime
     */
    private $createdAt;

    /**
     * @var Type
     */
    private $type;

    /**
     * @var User
     */
    private $admin;

    /**
     * @var Collection|User[]
     */
    private $users;

    public function __construct()
    {
        $this->createdAt = new DateTime();
    }

    /**
     * Get the value of wording
     *
     * @return  string
     */
    public function getWording()
    {
        return $this->wording;
    }

    /**
     * Set the value of wording
     *
     * @param  string  $wording
     *
     * @return  self
     */
    public function setWording(string $wording)
    {
        $this->wording = $wording;

        return $this;
    }

    /**
     * Get the value of inflows
     *
     * @return  int
     */
    public function getInflows()
    {
        return $this->inflows;
    }

    /**
     * Set the value of inflows
     *
     * @param  int  $inflows
     *
     * @return  self
     */
    public function setInflows(int $inflows)
    {
        $this->inflows = $inflows;

        return $this;
    }

    /**
     * Get the value of outflows
     *
     * @return  int
     */
    public function getOutflows()
    {
        return $this->outflows;
    }

    /**
     * Set the value of outflows
     *
     * @param  int  $outflows
     *
     * @return  self
     */
    public function setOutflows(int $outflows)
    {
        $this->outflows = $outflows;

        return $this;
    }

    /**
     * Get the value of balance
     *
     * @return  int
     */
    public function getBalance()
    {
        return $this->balance;
    }

    /**
     * Set the value of balance
     *
     * @param  int  $balance
     *
     * @return  self
     */
    public function setBalance(int $balance)
    {
        $this->balance = $balance;

        return $this;
    }

    /**
     * Get the value of observation
     *
     * @return  string
     */
    public function getObservation()
    {
        return $this->observation;
    }

    /**
     * Set the value of observation
     *
     * @param  string  $observation
     *
     * @return  self
     */
    public function setObservation(string $observation)
    {
        $this->observation = $observation;

        return $this;
    }

    /**
     * Get the value of createdAt
     *
     * @return  DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set the value of createdAt
     *
     * @param  DateTime  $createdAt
     *
     * @return  self
     */
    public function setCreatedAt(DateTime $createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get the value of type
     *
     * @return  Type
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set the value of type
     *
     * @param  Type  $type
     *
     * @return  self
     */
    public function setType(Type $type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get the value of admin
     *
     * @return  User
     */
    public function getAdmin()
    {
        return $this->admin;
    }

    /**
     * Set the value of admin
     *
     * @param  User  $admin
     *
     * @return  self
     */
    public function setAdmin(User $admin)
    {
        $this->admin = $admin;

        return $this;
    }

    /**
     * Get the value of user
     *
     * @return  User[]
     */
    public function getUsers()
    {
        return $this->users;
    }


    /**
     * Set the value of user
     *
     * @param  User[]  $user
     *
     * @return  self
     */
    public function setUsers($users)
    {
        $this->users = $users;

        return $this;
    }
}
