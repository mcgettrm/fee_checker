<?php

namespace Lendable\Interview\Strategies;

class LinearStrategy implements InterpolationStrategyInterface
{

    public function calculateFeeBetweenBreakPoints(int $lowerBound, float $progression, int $upperBound): float
    {
        $gapValue = $upperBound - $lowerBound;

        //What is the value of that progress, bearing in mind the gaps between break points vary and so do the corresponding fees
        $progressionFeeValue = $gapValue * $progression;

        return $lowerBound + $progressionFeeValue;
    }
}