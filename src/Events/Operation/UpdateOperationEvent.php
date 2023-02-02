<?php

namespace App\Events\Operation;

use App\Entity\Main\Funds\Fund;
use App\Entity\Main\Operations\Operation;

class UpdateOperationEvent
{
    use OperationLinked;

    public function __construct(private Operation $entity, private Operation $previous)
    {
    }

    public function getEntity(): Operation
    {
        return $this->entity;
    }

    public function getPrevious(): Operation
    {
        return $this->previous;
    }
}
