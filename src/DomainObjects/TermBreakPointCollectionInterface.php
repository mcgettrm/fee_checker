<?php

declare(strict_types=1);

namespace Lendable\Interview\DomainObjects;

use IteratorAggregate;
use Traversable;

/**
 * @extends IteratorAggregate<int,int>
 */
interface TermBreakPointCollectionInterface extends IteratorAggregate
{
    /** @return Traversable<int,int> */
    public function getIterator(): Traversable;

    public function getFeeAtBreakpoint(int $loanAmount): int|null;
}