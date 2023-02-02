<?php

namespace App\EventSubscriber\Exceptions;

use App\Exception\AppException;
use Exception;
use Throwable;

class RedirectRequestException extends AppException
{
    protected $route;

    protected $isPath;

    protected $params = [];

    public function __construct($route, ?string $message = null, bool $isPath = false, int $code = 0, ?Throwable $next = null)
    {
        if (!is_string($route) and !is_array($route)) {
            throw new AppException(__METHOD__ . 'argument "route" must be a string or an array');
        }
        if (is_string($route)) {
            $this->route = $route;
        } elseif (is_array($route)) {
            $this->route = $route[0];
            if (!is_array($route[1])) {
                throw new AppException('the second argument of ' . __METHOD__ . 'argument "route" must be array');
            }
            $this->params = $route[1];
        }

        $this->isPath = $isPath;
        $message = $message; //?? "The request was be redirect to a route \"$route\"";
        parent::__construct($message, $code, $next);
    }


    /**
     * Get the value of route
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * Get the value of isPath
     */
    public function getIsPath()
    {
        return $this->isPath;
    }

    /**
     * Get the value of params
     */
    public function getParams()
    {
        return $this->params;
    }
}
