<?php

namespace App\Events\Assistance;

trait AssistanceLinked
{
    public function isLinked(): bool
    {
        return $this->getContributor()->getFund() !== null;
    }
}
