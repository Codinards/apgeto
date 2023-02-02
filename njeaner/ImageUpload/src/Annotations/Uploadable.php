<?php

declare(strict_types=1);

namespace Njeaner\ImageUpload\Annotations;

use Doctrine\Common\Annotations\Annotation;
use Doctrine\Common\Annotations\Annotation\Target;

/**
 * @Annotation
 * @Target("CLASS")
 */
class Uploadable
{
    public function get()
    {
    }
}
