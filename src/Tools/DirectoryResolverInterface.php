<?php

namespace App\Tools;

interface DirectoryResolverInterface
{
    public static function getDirectory(?string $relativeDirectory = null): string;
}
