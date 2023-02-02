<?php

namespace App\Form\Extensions\Validations;

use App\Tools\Twig\LocalLanguages;
use App\Twig\TranslatorTwigExtension;

trait TranslatorTrait
{
    protected function trans(string $key, array $params = [], ?string $domain = null, ?string $local = null): string
    {
        return TranslatorTwigExtension::getInstance()
            ->__u($key, $params, $domain, $local ?? LocalLanguages::getInstance() ? LocalLanguages::getInstance()->getLocaleKey() : 'en');
    }
}
