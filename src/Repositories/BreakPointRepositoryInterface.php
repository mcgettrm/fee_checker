<?php

declare(strict_types=1);

namespace Lendable\Interview\Repositories;

use Lendable\Interview\DomainObjects\TermBreakPointCollectionInterface;
use Lendable\Interview\Utils\FeeStructureTermEnum;

interface BreakPointRepositoryInterface
{
    public function getBreakpointMappingForTerm(FeeStructureTermEnum $term): TermBreakPointCollectionInterface;
}