<?php

declare(strict_types=1);

namespace Njeaner\FrontTranslator;

use Doctrine\Common\Annotations\Annotation\Required;
use Njeaner\FrontTranslator\DependencyInjection\FrontTranslatorExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class FrontTranslatorBundle extends Bundle
{
    /**@Required */
    protected FrontTranslatorExtension $frontTranslatorExtension;

    public function build(ContainerBuilder $container)
    {
    }
}
