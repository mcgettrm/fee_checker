<?php

namespace Lendable\Interview\Strategies;

use Lendable\Interview\DomainObjects\TermBreakPointCollectionInterface;

interface FeeResolutionStrategyInterface
{
    public function calculateFeeForLoanAmount(
        int $loanAmount,
        TermBreakPointCollectionInterface $breakPointCollection
    ): int;
}