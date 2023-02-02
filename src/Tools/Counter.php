<?php

namespace App\Tools;

class Counter
{
    static $count = -1;

    public function __construct()
    {
    }

    public static function getCount()
    {
        self::$count++;
        return self::$count;
    }
}
