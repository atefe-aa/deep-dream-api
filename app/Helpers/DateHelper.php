<?php

namespace App\Helpers;

use Morilog\Jalali\Jalalian;

class DateHelper
{
    public static function toGregorian($jalali): string
    {
//        $jalali is in 1402/12/01 format
        [$year, $month, $day] = explode('/', $jalali);
        return (new Jalalian((int)$year, (int)$month, (int)$day))
            ->toCarbon()
            ->format('Y-m-d');
    }
}
