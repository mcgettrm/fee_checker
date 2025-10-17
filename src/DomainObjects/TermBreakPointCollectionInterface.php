<?php

declare(strict_types=1);

namespace Lendable\Interview\DomainObjects;

use IteratorAggregate;

interface TermBreakPointCollectionInterface extends IteratorAggregate
{
    public function getFeeAtBreakpoint(int $loanAmount): int|null;
}