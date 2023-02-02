<?php

namespace App\Subscriber;

use Illuminate\Support\Collection;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

abstract class AbstractEvent implements EventSubscriberInterface
{
    protected function collection(array $data = []): Collection
    {
        return new Collection($data);
    }
}
