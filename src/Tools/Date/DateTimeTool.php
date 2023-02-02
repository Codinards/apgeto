<?php

namespace App\Tools\Date;

use Carbon\Factory;

class DateTimeTool
{
    public static function getCarbonFactory(?string $locale = 'en_US', string $timezone = 'Africa/Brazzaville')
    {
        return new Factory([
            'locale' => $locale,
            'timezone' => $timezone,
        ]);
    }
}
