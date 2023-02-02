<?php

namespace App\Subscriber\Debt;

use App\Events\DebtRenewal\DeleteDebtRenewalEvent;
use App\Events\DebtRenewal\UpdateDebtRenewalEvent;
use App\Events\DebtRenewal\CreateDebtRenewalEvent;
use App\Subscriber\AbstractEvent;

class DebtRenewalSubscriber extends AbstractEvent
{
    public static function getSubscribedEvents()
    {
        return [
            CreateDebtRenewalEvent::class => "onCreating",
            UpdateDebtRenewalEvent::class => "onUpdating",
            DeleteDebtRenewalEvent::class => "onDeleting"
        ];
    }

    public function onCreating(CreateDebtRenewalEvent $event)
    {
    }

    public function onUpdating(UpdateDebtRenewalEvent $event)
    {
    }

    public function onDeleting(DeleteDebtRenewalEvent $event)
    {
    }
}
