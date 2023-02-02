<?php

namespace App\Entity\JsonEntity;

use App\Tools\DirectoryResolver;
use App\Tools\JsonFileManager;
use Doctrine\Common\Collections\ArrayCollection;
use Illuminate\Support\Collection;

class FunctionalitiesJsonEntity implements JsonEntityInterface
{
    private $filename = 'functionalities.json';

    private $fileManager;

    private $id;

    /**
     * @var ArrayCollection
     */
    private $contributors;

    public function __construct()
    {
        $this->contributors = new ArrayCollection();
        $filename = DirectoryResolver::getDirectory('var/configs') . $this->filename;
        $this->fileManager = new JsonFileManager($filename);
    }

    public function getFilename(): string
    {
        return $this->filename;
    }

    public function toArray(): array
    {
        $data = [];
        foreach ($this as $property => $value) {
            if ($property !== 'filename' and $property !== 'fileManager') {
                $method = 'get' . ucfirst($property);
                if (method_exists($this, $method)) {
                    if (is_object($value)) {
                        $value = $value->toArray();
                        foreach ($value as $k => $elt) {
                            $value[$k] = $elt->toArray();
                        }
                        $data[$property] = $value;
                    } else $data[$property] = $value;
                }
            }
        }
        return $data;
    }

    public function hasContributors()
    {
        return !$this->contributors->isEmpty();
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

    /**
     * Get the value of contributors
     */
    public function getContributors()
    {
        return $this->contributors;
    }

    /**
     * Set the value of contributors
     *
     * @return  self
     */
    public function setContributors($contributors)
    {
        $this->contributors = $contributors;

        return $this;
    }

    /**
     * Add the contributor in contributors
     *
     * @return  self
     */
    public function addContributors($contributor)
    {
        $this->contributors->add($contributor);

        return $this;
    }

    public function saveForce(array $data)
    {
        $this->fileManager->encode($data);
    }

    public function save()
    {
        $this->fileManager->encode([$this->id => $this->toArray()]);
    }

    public function getData(?int $id = null): array
    {
        $data = $this->fileManager->decode();
        if (!$id) return $data;
        if (isset($data[$id])) return (new Collection($data[$id])); //->keyBy(fn ($item) => $item['id']);

        return [];
    }
}
