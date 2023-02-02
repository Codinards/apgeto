<?php

namespace App\Events\Operation;

use App\Entity\Main\Operations\Operation;

class DeleteOperationEvent
{
    use OperationLinked;

    public function __construct(private Operation $entity)
    {
    }

    public function getEntity(): Operation
    {
        return $this->entity;
    }
}
