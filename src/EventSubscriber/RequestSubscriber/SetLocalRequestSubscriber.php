<?php

namespace App\EventSubscriber\RequestSubscriber;

use App\Tools\Entity\StaticEntityManager;
use App\Tools\Request\RequestLocalResolver;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SetLocalRequestSubscriber implements EventSubscriberInterface
{
    protected $staticManager;

    public function __construct(StaticEntityManager $staticManager)
    {
        $this->staticManager = $staticManager;
    }


    public function onKernelRequest(RequestEvent $event)
    {
        $request = $event->getRequest();
        RequestLocalResolver::resolveLocal($request);
    }

    public static function getSubscribedEvents()
    {
        return [
            'kernel.request' => 'onKernelRequest'
        ];
    }
}
