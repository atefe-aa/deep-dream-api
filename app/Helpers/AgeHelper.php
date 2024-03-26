<?php

namespace App\Helpers;


class AgeHelper
{
    public static function formatAge($ageYear, $ageMonth, $ageDay): array
    {
        $totalDays = 0;

        $totalDays += $ageYear * 365;

        $totalDays += $ageMonth * 30;

        $totalDays += $ageDay;

        $ageUnit = ($totalDays >= 365) ? 'year' : 'day';
        $age = ($ageUnit === 'year') ? floor($totalDays / 365) : $totalDays;

        return [
            'age' => $age,
            'ageUnit' => $ageUnit
        ];
    }
}
