<?php

namespace Lendable\Interview\Strategies;

interface RoundingStrategyInterface
{
    public function round(float $value): int;
}