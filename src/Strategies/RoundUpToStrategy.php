<?php

declare(strict_types=1);

namespace Lendable\Interview\Strategies;

class RoundUpToStrategy implements RoundingStrategyInterface
{
    public function __construct(private int $roundUpToNearest = 5)
    {
    }

    /**
     * Input expects pence value
     * @param float $value
     *
     * Returns a pence value
     * @return int
     */
    public function round(float $value): int
    {
        $baseFeePounds =
            $value / 100;
        $roundedUp = ceil($baseFeePounds / $this->roundUpToNearest) * $this->roundUpToNearest;

        //Convert back to pence
        return (int)round($roundedUp * 100);
    }
}