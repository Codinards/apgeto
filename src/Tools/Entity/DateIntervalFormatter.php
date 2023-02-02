<?php

namespace App\Tools\Entity;

class DateIntervalFormatter
{
    public static function toStringFormat(\DateInterval $dateInterval): string
    {
        $dateTime = explode(":", $dateInterval->format('%y-%m-%d:%H-%i-%s'));

        $date = '';
        $indexes = ['Y', 'M', 'D'];
        foreach(explode('-', $dateTime[0]) as $index => $elt){
            if((int) $elt !== 0){
                $date .= $indexes[$index] . $elt;
            }
        }
        $date = ($date === '') ? $date : "P" . $date;

        $time = '';
        $indexes = ['H', 'I', 'S'];

        foreach(explode('-', $dateTime[1]) as $index => $elt){
            if((int) $elt !== 0){
                $time .= $indexes[$index] . $elt;
            }
        }

        return $date . ($time !== "" ? 'T' . $time : '');
    }
}