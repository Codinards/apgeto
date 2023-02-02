<?php

namespace App\EventSubscriber\ControllerSubscriber;

use App\EventSubscriber\Exceptions\RedirectRequestException;
use App\Tools\Cache\CacheRetriever;
use App\Tools\Routes\Annotations\RouteActionManager;
use Symfony\Component\Cache\CacheItem;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\Security\Core\Security;

class UserActionControlSubscriber implements EventSubscriberInterface
{
    protected $routeActionManager;

    public function __construct(RouteActionManager $routeActionManager)
    {
        $this->routeActionManager = $routeActionManager;
    }


    public function onKernelController(ControllerEvent $event)
    {
        if (!$this->routeActionManager->actionIsAutorize($event->getRequest())) {
            throw new RedirectRequestException('home');
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            'kernel.controller' => 'onKernelController',
        ];
    }
}
