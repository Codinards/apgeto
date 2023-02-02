<?php

declare(strict_types=1);

namespace Njeaner\FrontTranslator\DependencyInjection;

use Exception;
use Njeaner\FrontTranslator\Manager\TranslationManager;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;

class FrontTranslatorExtension extends Extension
{

    private static ?string $translationPath = null;

    public function load(array $configs, ContainerBuilder $container)
    {
        TranslationManager::$configs = $configs[0];
    }

    /**
     * Get the value of translationPath
     */
    public function getTranslationPath(): ?string
    {
        if (null == self::$translationPath) throw new Exception();
        return self::$translationPath;
    }
}
