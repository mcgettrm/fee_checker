<?php

declare(strict_types=1);

namespace Lendable\Interview\DomainObjects;

use ArrayIterator;
use Traversable;

abstract class AbstractTermBreakPointCollection implements TermBreakPointCollectionInterface
{

    /**
     * @var array<int, int>
     */
    protected array $breakPoints = [];

    /**
     * @param array<int, int> $breakPoints
     */
    public function __construct(array $breakPoints)
    {
        $this->breakPoints = $breakPoints;
    }

    public function getFeeAtBreakpoint(int $loanAmount): int|null
    {
        if (array_key_exists($loanAmount, $this->breakPoints)) {
            return $this->breakPoints[$loanAmount];
        }
        return null;
    }

    /** @return Traversable<int,int> */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->breakPoints);
    }
}