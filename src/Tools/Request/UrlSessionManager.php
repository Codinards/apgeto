<?php

namespace App\Tools\Request;

use Serializable;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class UrlSessionManager
{
    protected $oldUrl;
    protected $session;

    protected $requestStack;

    public function __construct(SessionInterface $session, RequestStack $requestStack)
    {
        $this->session = $session;
        $this->requestStack = $requestStack;
    }

    public function set(string $oldUrl)
    {
        $this->session->set('old_route', $oldUrl);
    }

    public function getOldUrl()
    {
        return $this->session->get('old_route');
    }

    public function hasOldUrl()
    {
        return $this->session->has('old_route');
    }

    public function getRequestPath(): string
    {
        return $this->requestStack->getMasterRequest()->getPathInfo();
    }

    public function putUrl()
    {
        $url = $this->getRequestPath();
        $oldUrl = $this->getOldUrl();
        if ($url !== $oldUrl) {
            $this->set($url);
        }
    }
}
