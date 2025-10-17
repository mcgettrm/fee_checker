<?php

declare(strict_types=1);

namespace Lendable\Interview\Tests\DomainObjects;

use Lendable\Interview\DomainObjects\FeeStructure;
use Lendable\Interview\DomainObjects\TermBreakPointCollectionTwentyFour;
use Lendable\Interview\Strategies\LinearRoundUpNearestFiveStrategy;
use Lendable\Interview\Utils\FeeStructureTermEnum;
use PHPUnit\Framework\TestCase;

class FeeStructureTest extends TestCase
{
    public function testFeeReturnsOnBreakpoint()
    {
        $loanAmount = 100000;
        $expectedFee = 7000;
        $feeStructure = new FeeStructure(
            FeeStructureTermEnum::TwentyFourMonth,
            new TermBreakPointCollectionTwentyFour(),
            new LinearRoundUpNearestFiveStrategy()
        );
        $actualFee = $feeStructure->getFeeForLoanAmount($loanAmount);
        $this->assertEquals(
            $expectedFee,
            $actualFee,
            'The fee structure should return exactly the expected fee if the requested loan amount is present in the mapping'
        );
    }
}