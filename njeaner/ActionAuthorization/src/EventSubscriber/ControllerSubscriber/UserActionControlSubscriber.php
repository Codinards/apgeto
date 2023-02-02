<?php

namespace Njeaner\ActionAuthorization\EventSubscriber\ControllerSubscriber;

use App\EventSubscriber\Exceptions\RedirectRequestException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;

class UserActionControlSubscriber implements EventSubscriberInterface
{



    public function onKernelController(ControllerEvent $event)
    {
        dd($this);
    }

    public static function getSubscribedEvents()
    {
        return [
            'kernel.controller' => 'onKernelController',
        ];
    }
}
