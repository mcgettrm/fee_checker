<?php

declare(strict_types=1);

namespace Lendable\Interview\Repositories;

use Lendable\Interview\DomainObjects\FeeStructureInterface;
use Lendable\Interview\DomainObjects\TermBreakPointCollectionInterface;
use Lendable\Interview\Utils\FeeStructureTermEnum;

interface BreakPointRepositoryInterface
{
    public function getFeeStructureByTerm(FeeStructureTermEnum $term): FeeStructureInterface;
    
    public function getBreakpointMappingForTerm(FeeStructureTermEnum $term): TermBreakPointCollectionInterface;
}