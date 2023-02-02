<?php

namespace App\Twig;

use App\Tools\AppConstants;
use App\Tools\Twig\LocalLanguages;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class NumberTwigExtension extends AbstractExtension
{
    protected $localLanguage;

    protected $locale;

    protected $decPoint;

    public function __construct(LocalLanguages $localLanguage)
    {
        $this->localLanguage = $localLanguage;
    }


    public function getFilters(): array
    {
        return [
            new TwigFilter('number_format', [$this, 'numberFormat']),
            new TwigFilter('money_format', [$this, 'moneyFormat']),
            new TwigFilter('telephone_format', [$this, 'telephoneFormat']),
        ];
    }

    public function numberFormat(?float $number, int $decimals = 0)
    {
        $number = (int) $number;
        $this->resolveDecPoint();
        return number_format($number, $decimals, $this->decPoint, ' ');
    }

    public function moneyFormat(?float $number, int $decimals = 0)
    {
        return $this->numberFormat($number, $decimals) . ' ' . AppConstants::$MONEY_DEVISE;
    }

    public function telephoneFormat($telephone)
    {
        $telephone = (int) str_replace([' ', '-', '.'], '', (string)$telephone);
        return $this->numberFormat($telephone);
    }

    private function getLocale()
    {
        if ($this->locale === null) {
            $this->locale = $this->localLanguage->getLocale();
        }
        return $this->locale;
    }

    private function resolveDecPoint()
    {
        $locale = $this->getLocale();
        if (!$locale == LocalLanguages::ENGLISH) {
            return $this->decPoint = ',';
        }
        return $this->decPoint = '.';
    }
}
