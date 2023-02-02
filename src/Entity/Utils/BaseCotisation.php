<?php

namespace App\Entity\Utils;

use App\Entity\Tontines\CotisationDay;
use DateTime;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;

class BaseCotisation
{
    /**
     * @var Collection
     */
    private Collection $items;

    private CotisationDay $date;

    public function __construct(array $items)
    {
        $data = new ArrayCollection();
        foreach ($items as $key => $item) {
            $data->add(
                (new TontineCotisation($item))->setName($key)
            );
        }

        $this->items = $data;
    }

    public function handleFailure(EntityManagerInterface $manager): self
    {
        $manager->persist($this->date);
        foreach ($this->items as $item) {
            $item->handleFailure($manager, $this->date);
        }
        return $this;
    }

    /**
     * Get the value of items
     *
     * @return  Collection
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * Set the value of items
     *
     * @param  Collection  $items
     *
     * @return  self
     */
    public function setItems(Collection $items)
    {
        $this->items = $items;

        return $this;
    }

    /**
     * Get the value of date
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set the value of date
     *
     * @return  self
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }
}
