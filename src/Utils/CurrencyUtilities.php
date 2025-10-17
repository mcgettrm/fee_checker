<?php

declare(strict_types=1);

namespace Lendable\Interview\Utils;

class CurrencyUtilities
{
    public static function convertStringToPence(string $string): int
    {
        $clean = str_replace([',', '£', ' '], '', $string);
        return (int)round((float)$clean * 100);
    }

    public static function convertPenceToDisplay(int $pence): string
    {
        //Needs to be divided by 100
        $float = round($pence / 100, 2);
        return number_format($float, 2, '.', ',');
    }
}