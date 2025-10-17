<?php

declare(strict_types=1);

namespace Lendable\Interview\Strategies;

use Lendable\Interview\DomainObjects\TermBreakPointCollectionInterface;

interface FeeResolutionStrategyInterface
{
    public function calculateFeeForLoanAmount(
        int $loanAmount,
        TermBreakPointCollectionInterface $breakPointCollection
    ): int;
}