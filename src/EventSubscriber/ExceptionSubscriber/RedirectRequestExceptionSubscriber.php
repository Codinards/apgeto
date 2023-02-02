<?php

namespace App\EventSubscriber\ExceptionSubscriber;

use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use App\EventSubscriber\Exceptions\RedirectRequestException;
use App\Exception\StartAppRuntimeException;
use Doctrine\DBAL\Exception\ConnectionException;
use Error;
use Symfony\Component\ErrorHandler\Error\FatalError;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class RedirectRequestExceptionSubscriber implements EventSubscriberInterface
{
    protected $router;
    static $count = 0;

    const LANCHING = [
        "configuration de l'application",
        "connection a la base de donnees",
        "configuration de la base de donnees",
        "lancement de l'application"
    ];

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function onKernelException(ExceptionEvent $event)
    {

        $exception = $event->getThrowable();
        $lang = $event->getRequest()->getLocale();
        // if ($exception instanceof ConnectionException) {
        //     if ($event->getRequest()->query->get("launching_app")) {
        //         echo "<h1 style'text:center;padding-top:40px'>
        //         Veuillez patienter durant le demarrage du programme
        //         </h1>";
        //         if (self::$count === 10) {
        //             dd($event);
        //         }
        //         header("HTTP/1.1 301 Moved Permanently");
        //         header("Location: " .  $event->getRequest()->getUri() . "-" . self::$count++);
        //         exit;
        //         //         sleep(10);
        //         //         $event->setResponse(
        //         //             new RedirectResponse(
        //         //                 $event->getRequest()->getUri() . "&action=" . self::LANCHING[self::$count]
        //         //             )
        //         //         );
        //         //         self::$count++;
        //         //         if (self::$count >= 4) self::$count = 3;
        //     }
        // }
        // if (self::$count !== 0) {
        //     self::$count = 0;
        //     $event->setResponse(
        //         new RedirectResponse("/")
        //     );
        // }
        if ($exception instanceof RedirectRequestException) {
            $params = $exception->getParams();
            $params['_locale'] = $lang;
            $event->setResponse(
                new RedirectResponse(
                    $exception->getIsPath() ? $exception->getRoute() :
                        $this->router->generate($exception->getRoute(), $params)
                )
            );
            /** @var Session $session */
            $session = $event->getRequest()->getSession();
            if (!empty($message = $exception->getMessage())) {
                $session->getFlashBag()->add('error', $message);
            }
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            'kernel.exception' => 'onKernelException',
        ];
    }
}
