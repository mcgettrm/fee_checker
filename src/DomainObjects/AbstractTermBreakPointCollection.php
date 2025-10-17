<?php

namespace Lendable\Interview\DomainObjects;

use ArrayIterator;

abstract class AbstractTermBreakPointCollection implements TermBreakPointCollectionInterface
{
    protected array $breakPoints = [];

    public function getFeeAtBreakpoint(int $loanAmount): int|null
    {
        if (array_key_exists($loanAmount, $this->breakPoints)) {
            return $this->breakPoints[$loanAmount];
        }
        return null;
    }

    public function getIterator(): \Traversable
    {
        return new ArrayIterator($this->breakPoints);
    }
}