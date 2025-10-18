<?php

namespace Lendable\Interview\Factories;

use Lendable\Interview\DomainObjects\FeeStructureInterface;
use Lendable\Interview\DomainObjects\TermBreakPointCollectionInterface;
use Lendable\Interview\Utils\FeeStructureTermEnum;

interface FeeStructureFactoryInterface
{
    public function getFeeStructure(
        FeeStructureTermEnum $term,
        TermBreakPointCollectionInterface $breakpoints
    ): FeeStructureInterface;
}