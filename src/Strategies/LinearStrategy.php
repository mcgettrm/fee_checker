<?php

declare(strict_types=1);

namespace Lendable\Interview\Strategies;

class LinearStrategy implements InterpolationStrategyInterface
{

    public function calculateFeeBetweenBreakPoints(int $lowerBound, float $progression, int $upperBound): float
    {
        if ($progression < 0) {
            throw new \Exception("Linear interpolation progression must not be negative. Received: $progression");
        }
        $gapValue = $upperBound - $lowerBound;

        //What is the value of that progress, bearing in mind the gaps between break points vary and so do the corresponding fees
        $progressionFeeValue = $gapValue * $progression;

        return $lowerBound + $progressionFeeValue;
    }
}