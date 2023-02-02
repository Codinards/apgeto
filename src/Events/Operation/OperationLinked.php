<?php

namespace App\Events\Operation;

trait OperationLinked
{
    public function isLinked(): bool
    {
        return $this->getEntity()->getFund() !== null;
    }
}
