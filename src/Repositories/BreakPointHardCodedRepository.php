<?php

declare(strict_types=1);

namespace Lendable\Interview\Repositories;

use Lendable\Interview\DomainObjects\BreakPointCollection;
use Lendable\Interview\DomainObjects\TermBreakPointCollectionInterface;
use Lendable\Interview\Utils\FeeStructureTermEnum;

class BreakPointHardCodedRepository implements BreakPointRepositoryInterface
{
    /**
     * @param FeeStructureTermEnum $term
     * @return TermBreakPointCollectionInterface
     */
    public function getBreakpointMappingForTerm(FeeStructureTermEnum $term): TermBreakPointCollectionInterface
    {
        return match ($term) {
            FeeStructureTermEnum::TwelveMonth => new BreakPointCollection([
                100000 => 5000,
                200000 => 9000,
                300000 => 9000,
                400000 => 11500,
                500000 => 10000,
                600000 => 12000,
                700000 => 14000,
                800000 => 16000,
                900000 => 18000,
                1000000 => 20000,
                1100000 => 22000,
                1200000 => 24000,
                1300000 => 26000,
                1400000 => 28000,
                1500000 => 30000,
                1600000 => 32000,
                1700000 => 34000,
                1800000 => 36000,
                1900000 => 38000,
                2000000 => 40000,
            ]),
            FeeStructureTermEnum::TwentyFourMonth => new BreakPointCollection([
                100000 => 7000,
                200000 => 10000,
                300000 => 12000,
                400000 => 16000,
                500000 => 20000,
                600000 => 24000,
                700000 => 28000,
                800000 => 32000,
                900000 => 36000,
                1000000 => 40000,
                1100000 => 44000,
                1200000 => 48000,
                1300000 => 52000,
                1400000 => 56000,
                1500000 => 60000,
                1600000 => 64000,
                1700000 => 68000,
                1800000 => 72000,
                1900000 => 76000,
                2000000 => 80000,
            ]),
            //No default required here - PHPStan needs to stay happy
        };
    }

}