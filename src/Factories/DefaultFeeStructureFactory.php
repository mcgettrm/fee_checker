<?php

declare(strict_types=1);

namespace Lendable\Interview\Factories;

use Lendable\Interview\DomainObjects\FeeStructure;
use Lendable\Interview\DomainObjects\TermBreakPointCollectionInterface;
use Lendable\Interview\Strategies\LinearRoundUpStrategy;
use Lendable\Interview\Utils\FeeStructureTermEnum;

class DefaultFeeStructureFactory implements FeeStructureFactoryInterface
{
    public function getFeeStructure(
        FeeStructureTermEnum $term,
        TermBreakPointCollectionInterface $breakpoints
    ): FeeStructure {
        return new FeeStructure($term, $breakpoints, new LinearRoundUpStrategy());
    }
}