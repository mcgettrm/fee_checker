<?php

namespace Lendable\Interview\DomainObjects;

use Lendable\Interview\Strategies\FeeResolutionStrategyInterface;
use Lendable\Interview\Utils\FeeStructureTermEnum;

class FeeStructure implements FeeStructureInterface
{

    public function __construct(
        private FeeStructureTermEnum $term,
        private TermBreakPointCollectionInterface $termBreakPointCollection,
        private FeeResolutionStrategyInterface $strategy,
    ) {
    }

    public function getFeeForLoanAmount(int $loanAmount): int
    {
        // TODO: Implement getFeeForLoanAmount() method.
    }
}