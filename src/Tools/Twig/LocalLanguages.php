<?php

namespace App\Tools\Twig;

use App\Tools\Languages\LangResolver;
use App\Twig\TranslatorTwigExtension;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class LocalLanguages
{
    const ENGLISH = 'en';
    const FRENCH = 'fr';
    const SPAIN = 'es';

    private $request;

    protected $requestStack;

    protected static $instance;

    protected $translatorTwigExtension;

    public function __construct(RequestStack $requestStack, TranslatorTwigExtension $translatorTwigExtension)
    {
        $this->requestStack = $requestStack;

        self::$instance = $this;
        $this->translatorTwigExtension = $translatorTwigExtension;
    }

    public function getLanguages()
    {
        return LangResolver::$languages_name;
    }

    public function getRoute()
    {
        $request = $this->getRequest();
        return $request->attributes->get('_route');
    }

    public function getRouteParams()
    {
        $request = $this->getRequest();
        return array_merge($request->attributes->get('_route_params'), $request->query->all());
    }

    public function getRequest(): ?Request
    {
        if (is_null($this->request)) {
            $this->request = $this->requestStack->getMasterRequest();
        }
        return $this->request;
    }

    public function getLocale()
    {
        return $this->getRequest()->getLocale();

        // return LangResolver::getLanguage($locale);
    }

    public function getLocaleKey()
    {
        return $this->getRequest()->getLocale();

        // return LangResolver::getkey($locale);
    }

    /**
     * Get the value of instance
     */
    public static function getInstance(): self
    {
        return self::$instance;
    }
}
