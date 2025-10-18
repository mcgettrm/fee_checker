<?php

declare(strict_types=1);

namespace Lendable\Interview\Strategies;

interface InterpolationStrategyInterface
{
    public function calculateFeeBetweenBreakPoints(int $lowerBound, float $progression, int $upperBound): float;
}