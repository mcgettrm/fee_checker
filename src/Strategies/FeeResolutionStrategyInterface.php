<?php

declare(strict_types=1);

namespace Lendable\Interview\Strategies;

use Lendable\Interview\DomainObjects\TermBreakPointCollectionInterface;
use Money\Money;

interface FeeResolutionStrategyInterface
{
    public function calculateFeeForLoanAmount(
        Money $loanAmount,
        TermBreakPointCollectionInterface $breakPointCollection
    ): Money;
}