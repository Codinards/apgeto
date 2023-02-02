<?php

namespace App\Tools\Routes\Annotations;

use Symfony\Component\Routing\Route;

class RouteAction
{
    protected $route;

    protected $name;

    public function __construct(Route $route, string $name)
    {
        $this->route = $route;
        $this->name = $name;
    }

    /**
     * Get the value of route
     */
    public function getRoute(): Route
    {
        return $this->route;
    }

    /**
     * Get the value of name
     */
    public function getName(): string
    {
        return $this->name;
    }

    public function getOption(string $name)
    {
        return $this->route->getOption($name);
    }

    public function getOptions()
    {
        return $this->route->getOptions();
    }

    public static function routesCollection(array $routes): array
    {
        $routesAction = [];
        foreach ($routes as $key => $route) {
            $routesAction[] = new RouteAction($route, $key);
        }
        return $routesAction;
    }
}
