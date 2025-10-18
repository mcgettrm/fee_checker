<?php

declare(strict_types=1);

namespace Lendable\Interview\Tests\Strategies;

use Lendable\Interview\DomainObjects\TermBreakPointCollectionInterface;
use Lendable\Interview\Factories\DefaultFeeStructureFactory;
use Lendable\Interview\Repositories\BreakPointHardCodedRepository;
use Lendable\Interview\Strategies\LinearRoundUpNearestFiveStrategy;
use Lendable\Interview\Tests\Fixtures\UnorderedBreakPointCollection;
use Lendable\Interview\Utils\FeeStructureTermEnum;
use PHPUnit\Framework\TestCase;

class LinearRoundUpNearestFiveStrategyTest extends TestCase
{


    private TermBreakPointCollectionInterface $breakPoints24;
    private TermBreakPointCollectionInterface $breakPoints12;

    public function setUp(): void
    {
        parent::setUp();
        $breakPointRepository = new BreakPointHardCodedRepository(new DefaultFeeStructureFactory());
        $this->breakPoints24 = $breakPointRepository->getBreakpointMappingForTerm(
            FeeStructureTermEnum::TwentyFourMonth
        );
        $this->breakPoints12 = $breakPointRepository->getBreakpointMappingForTerm(FeeStructureTermEnum::TwelveMonth);
    }

    public function testFeeReturnsOnBreakpoint(): void
    {
        $loanAmount = 100000;
        $expectedFee = 7000;
        $strategy = new LinearRoundUpNearestFiveStrategy();
        $actualFee = $strategy->calculateFeeForLoanAmount($loanAmount, $this->breakPoints24);
        $this->assertEquals(
            $expectedFee,
            $actualFee,
            'The strategy should return exactly the expected fee if the requested loan amount is present in the mapping'
        );
    }

    public function testFeeReturnsLinearOffBreakpointFeeGap40(): void
    {
        $loanAmount = 1150000;
        $expectedFee = 46000;
        $strategy = new LinearRoundUpNearestFiveStrategy();
        $actualFee = $strategy->calculateFeeForLoanAmount($loanAmount, $this->breakPoints24);
        $this->assertEquals(
            $expectedFee,
            $actualFee,
            'The strategy should be able to calculate a fee using a linear calculation between breakpoints'
        );
    }

    public function testFeeReturnsLinearOffBreakpointFeeGap30(): void
    {
        $loanAmount = 150000;
        $expectedFee = 8500;
        $strategy = new LinearRoundUpNearestFiveStrategy();
        $actualFee = $strategy->calculateFeeForLoanAmount($loanAmount, $this->breakPoints24);
        $this->assertEquals(
            $expectedFee,
            $actualFee,
            'The strategy should be able to calculate a fee using a linear calculation between breakpoints'
        );
    }

    public function testFeeReturnsLinearOffBreakpointFeeGap30WithRoundUpNearestFive(): void
    {
        $loanAmount = 140000;
        $expectedFee = 8500;
        $strategy = new LinearRoundUpNearestFiveStrategy();
        $actualFee = $strategy->calculateFeeForLoanAmount($loanAmount, $this->breakPoints24);
        $this->assertEquals(
            $expectedFee,
            $actualFee,
            'The strategy should be rounding up the calculated fee to the nearest multiple of 5'
        );
    }

    public function testFeeReturnsLinearOffBreakpointFeeGap30WithRoundUpNearestFiveMultipleOfTen(): void
    {
        $loanAmount = 130000;
        $expectedFee = 8000;
        $strategy = new LinearRoundUpNearestFiveStrategy();
        $actualFee = $strategy->calculateFeeForLoanAmount($loanAmount, $this->breakPoints24);
        $this->assertEquals(
            $expectedFee,
            $actualFee,
            'The strategy should be rounding up the calculated fee to the nearest multiple of 5'
        );
    }

    public function testLinearRoundupFiveWithGivenInputForTwelveMonthFeeStructure(): void
    {
        $loanAmount = 1925000;
        $expectedFee = 38500;
        $strategy = new LinearRoundUpNearestFiveStrategy();
        $actualFee = $strategy->calculateFeeForLoanAmount($loanAmount, $this->breakPoints12);
        $this->assertEquals(
            $expectedFee,
            $actualFee,
            'The strategy should be rounding up the calculated fee to the nearest multiple of 5'
        );
    }

    public function testUnorderedBreakPointsDoNotCauseException(): void
    {
        //£18,000
        $loanAmount = 1800000;
        //£180
        $expectedFee = 18000;
        $strategy = new LinearRoundUpNearestFiveStrategy();
        $unorderedBreakPointCollection = new UnorderedBreakPointCollection([
            100000 => 1000,
            300000 => 3000,
            200000 => 2000,
            2000000 => 20000,
            400000 => 4000,
        ]);
        $actualFee = $strategy->calculateFeeForLoanAmount($loanAmount, $unorderedBreakPointCollection);
        $this->assertEquals(
            $expectedFee,
            $actualFee,
            'The strategy should the breakpoints prior to running its algorithm'
        );
    }
}