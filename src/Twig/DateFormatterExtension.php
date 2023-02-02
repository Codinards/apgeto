<?php

namespace App\Twig;

use App\Tools\Date\DateTimeTool;
use App\Tools\Languages\LangResolver;
use Carbon\Factory;
use Twig\TwigFilter;
use Carbon\Carbon;
use DateTime;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Extension\AbstractExtension;

class DateFormatterExtension extends AbstractExtension
{
    /**
     *
     * @var Factory
     */
    protected $factory;

    protected $requestStack;

    static $instance;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
        self::$instance = $this;
    }


    public function getFilters(): array
    {
        return [
            new TwigFilter('in_locale', [$this, 'dateInLocale']),
        ];
    }


    public function dateInLocale(?DateTime $dateTime, string $format = 'll')
    {
        if (null === $dateTime) {
            return '';
        }
        $locale = LangResolver::getLanguage($this->requestStack->getCurrentRequest()->getLocale());
        $this->factory = DateTimeTool::getCarbonFactory($locale);
        $dateTime = new Carbon($dateTime);
        return $this->factory->make($dateTime)->isoFormat($format);
    }

    public static function getInstance(): self
    {
        if (is_null(self::$instance)) {
            throw new TwigExtensionException();
        }
        return self::$instance;
    }
}
