<?php

namespace App\Entity\Utils;

use App\Entity\Tontines\CotisationDay;
use App\Entity\Tontines\TontineurData;
use App\Entity\Utils\DataCotisation;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;

class TontineCotisation
{
    private collection $data;

    private string $name;

    public function __construct(array $data)
    {
        $arrayData = [];
        foreach ($data as $item) {
            $arrayData[] = (new DataCotisation($item->getUnities()->toArray()))->setTontiner($item);
        }
        $this->data = new ArrayCollection($arrayData);
    }

    public function handleFailure(EntityManagerInterface $manager, CotisationDay $day)
    {
        /** @var DataCotisation $data */
        foreach ($this->data as $data) {
            $data->handleFailure($manager, $day);
        }
    }

    /**
     * Get the value of data
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Set the value of data
     *
     * @return  self
     */
    public function setData($data)
    {
        $this->data = $data;

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
}
