<?php

namespace App\Listeners\EntityListeners;

use App\Entity\Main\Funds\Debt;
use Doctrine\ORM\Event\LifecycleEventArgs;

class DebtListener
{
    public function postLoad(LifecycleEventArgs $event)
    {

        /** @var Debt $debt */
        $debt = $event->getObject();
        $isdebt = $debt instanceof Debt;
        if ($isdebt) {
            $debt->setRenewalDate();
            $debt->setIsRenewable();
        }
    }
}
