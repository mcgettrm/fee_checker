<?php

declare(strict_types=1);

namespace Lendable\Interview\Repositories;

use Lendable\Interview\DomainObjects\FeeStructure;
use Lendable\Interview\DomainObjects\FeeStructureInterface;
use Lendable\Interview\DomainObjects\TermBreakPointCollectionInterface;
use Lendable\Interview\DomainObjects\TermBreakPointCollectionTwelve;
use Lendable\Interview\DomainObjects\TermBreakPointCollectionTwentyFour;
use Lendable\Interview\Strategies\LinearRoundUpNearestFiveStrategy;
use Lendable\Interview\Utils\FeeStructureTermEnum;

class FeeStructureHardCodedRepository implements FeeStructureRepositoryInterface
{

    public function getFeeStructureByTerm(FeeStructureTermEnum $term): FeeStructureInterface
    {
        //TODO::Make this more DI-able? What if we want a different strategy?
        $strategy = new LinearRoundUpNearestFiveStrategy();
        $mapping = $this->getBreakpointMappignForTerm($term);
        return new FeeStructure($term, $mapping, $strategy);
    }

    /**
     * @param FeeStructureTermEnum $term
     * @return TermBreakPointCollectionInterface
     */
    private function getBreakpointMappignForTerm(FeeStructureTermEnum $term): TermBreakPointCollectionInterface
    {
        //TODO::What if we have 6, or 36, too? How to make this resilient to change? Check whether the mapping exists in the filestystem maybe?
        //TODO::Does this need to stay like this because we don't have a persistent layer?
        if ($term === FeeStructureTermEnum::TwelveMonth) {
            return new TermBreakPointCollectionTwelve();
        } else {
            return new TermBreakPointCollectionTwentyFour();
        }
    }

}