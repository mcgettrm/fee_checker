<?php

namespace Lendable\Interview\Tests\Strategy;

use Lendable\Interview\DomainObjects\TermBreakPointCollectionTwelve;
use Lendable\Interview\DomainObjects\TermBreakPointCollectionTwentyFour;
use Lendable\Interview\Strategies\LinearRoundUpNearestFiveStrategy;
use PHPUnit\Framework\TestCase;

class LinearRoundupNearestFiveStrategyTest extends TestCase
{
    public function testFeeReturnsOnBreakpoint()
    {
        $loanAmount = 100000;
        $expectedFee = 7000;
        $termBreakpoints = new TermBreakPointCollectionTwentyFour();
        $strategy = new LinearRoundUpNearestFiveStrategy();
        $actualFee = $strategy->calculateFeeForLoanAmount($loanAmount, $termBreakpoints);
        $this->assertEquals(
            $expectedFee,
            $actualFee,
            'The strategy should return exactly the expected fee if the requested loan amount is present in the mapping'
        );
    }

    public function testFeeReturnsLinearOffBreakpointFeeGap40()
    {
        $loanAmount = 1150000;
        $expectedFee = 46000;
        $termBreakpoints = new TermBreakPointCollectionTwentyFour();
        $strategy = new LinearRoundUpNearestFiveStrategy();
        $actualFee = $strategy->calculateFeeForLoanAmount($loanAmount, $termBreakpoints);
        $this->assertEquals(
            $expectedFee,
            $actualFee,
            'The strategy should be able to calculate a fee using a linear calculation between breakpoints'
        );
    }

    public function testFeeReturnsLinearOffBreakpointFeeGap30()
    {
        $loanAmount = 150000;
        $expectedFee = 8500;
        $termBreakpoints = new TermBreakPointCollectionTwentyFour();
        $strategy = new LinearRoundUpNearestFiveStrategy();
        $actualFee = $strategy->calculateFeeForLoanAmount($loanAmount, $termBreakpoints);
        $this->assertEquals(
            $expectedFee,
            $actualFee,
            'The strategy should be able to calculate a fee using a linear calculation between breakpoints'
        );
    }

    public function testFeeReturnsLinearOffBreakpointFeeGap30WithRoundUpNearestFive()
    {
        $loanAmount = 140000;
        $expectedFee = 8500;
        $termBreakpoints = new TermBreakPointCollectionTwentyFour();
        $strategy = new LinearRoundUpNearestFiveStrategy();
        $actualFee = $strategy->calculateFeeForLoanAmount($loanAmount, $termBreakpoints);
        $this->assertEquals(
            $expectedFee,
            $actualFee,
            'The strategy should be rounding up the calculated fee to the nearest multiple of 5'
        );
    }

    public function testFeeReturnsLinearOffBreakpointFeeGap30WithRoundUpNearestFiveMultipleOfTen()
    {
        $loanAmount = 130000;
        $expectedFee = 8000;
        $termBreakpoints = new TermBreakPointCollectionTwentyFour();
        $strategy = new LinearRoundUpNearestFiveStrategy();
        $actualFee = $strategy->calculateFeeForLoanAmount($loanAmount, $termBreakpoints);
        $this->assertEquals(
            $expectedFee,
            $actualFee,
            'The strategy should be rounding up the calculated fee to the nearest multiple of 5'
        );
    }

    public function testLinearRoundupFiveWithGivenInputForTwelveMonthFeeStructure()
    {
        $loanAmount = 1925000;
        $expectedFee = 38500;
        $termBreakpoints = new TermBreakPointCollectionTwelve();
        $strategy = new LinearRoundUpNearestFiveStrategy();
        $actualFee = $strategy->calculateFeeForLoanAmount($loanAmount, $termBreakpoints);
        $this->assertEquals(
            $expectedFee,
            $actualFee,
            'The strategy should be rounding up the calculated fee to the nearest multiple of 5'
        );
    }
}