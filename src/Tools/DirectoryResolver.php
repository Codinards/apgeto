<?php

namespace App\Tools;

class DirectoryResolver implements DirectoryResolverInterface
{
    public static function getDirectory(?string $relativeDirectory = null, bool $end_slash = true): string
    {
        $baseDir = dirname(dirname(__DIR__));
        if (file_exists($dir = $baseDir . ($relativeDirectory ? '/' . $relativeDirectory : ''))) {
            return $dir . ($end_slash ? '/' : '');
        }
        throw new DirectoryResolverException("Directory \"$dir\" does not exist");
    }
}
