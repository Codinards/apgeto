<?php

namespace App\Entity\JsonEntity;

class ContributorJsonEntity //implements JsonEntityInterface
{
    //private $filename = 'user_roles.json';

    private $id;

    private $user_id;

    private $amount;

    /*public function getFilename(): string
    {
        return $this->filename;
    }*/

    public function toArray(): array
    {
        $data = [];
        foreach ($this as $property => $value) {
            if ($property !== 'filename') {
                $method = 'get' . ucfirst($property);
                if (method_exists($this, $method)) {
                    $data[$property] = $value;
                }
            }
        }
        return $data;
    }

    public function getPrimaryKey()
    {
        return 'id';
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
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * Set the value of name
     *
     * @return  self
     */
    public function setUserId(int $user_id)
    {
        $this->user_id = $user_id;

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
    public function setAmount(int $amount)
    {
        $this->amount = $amount;

        return $this;
    }

    public function __toString()
    {
        return $this->id;
    }
}
