<?php

declare(strict_types=1);

namespace Lendable\Interview\utils;

class CurrencyUtilities
{
    public static function convertStringToPense(string $string): int
    {
        $clean = str_replace([',', '£', ' '], '', $string);
        return (int)round((float)$clean * 100);
    }

    public static function convertPenseToDisplay(int $pense): string
    {
        //Needs to be divided by 100
        $float = round($pense / 100, 2);
        return number_format($float, 2, '.', ',');
    }
}