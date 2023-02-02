<?php

namespace App\Tools\Request;

use Symfony\Component\HttpFoundation\Request;

class RequestLocalResolver
{
    public static function resolveLocal(Request &$request): Request
    {
        /*$pathInfo = $request->getPathInfo();
        if (preg_match('/([a-z]{2}_[A-Z]{2})/', $pathInfo, $matches)) {
            $request->setDefaultLocale($matches[0]);
        }*/
        return $request;
    }
}
