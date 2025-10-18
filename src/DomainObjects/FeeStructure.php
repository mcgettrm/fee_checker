<?php

declare(strict_types=1);

namespace Lendable\Interview\DomainObjects;

use Lendable\Interview\Strategies\FeeResolutionStrategyInterface;
use Lendable\Interview\Utils\FeeStructureTermEnum;
use Money\Money;

class FeeStructure implements FeeStructureInterface
{

    public function __construct(
        private FeeStructureTermEnum $term,
        private TermBreakPointCollectionInterface $termBreakPointCollection,
        private FeeResolutionStrategyInterface $strategy,
    ) {
    }

    public function getFeeForLoanAmount(Money $loanAmount): Money
    {
        return $this->strategy->calculateFeeForLoanAmount($loanAmount, $this->termBreakPointCollection);
    }

    public function getTermIdentifier(): FeeStructureTermEnum
    {
        return $this->term;
    }
}