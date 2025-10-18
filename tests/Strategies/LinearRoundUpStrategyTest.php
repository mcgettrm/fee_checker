<?php

declare(strict_types=1);

namespace Lendable\Interview\Tests\Strategies;

use Lendable\Interview\DomainObjects\TermBreakPointCollectionInterface;
use Lendable\Interview\Repositories\BreakPointHardCodedRepository;
use Lendable\Interview\Strategies\LinearRoundUpStrategy;
use Lendable\Interview\Strategies\LinearStrategy;
use Lendable\Interview\Strategies\RoundUpToStrategy;
use Lendable\Interview\Tests\Fixtures\UnorderedBreakPointCollection;
use Lendable\Interview\Utils\CurrencyUtilities;
use Lendable\Interview\Utils\FeeStructureTermEnum;
use PHPUnit\Framework\TestCase;

class LinearRoundUpStrategyTest extends TestCase
{
    private TermBreakPointCollectionInterface $breakPoints24;
    private TermBreakPointCollectionInterface $breakPoints12;

    private LinearRoundUpStrategy $strategy;

    public function setUp(): void
    {
        parent::setUp();
        $breakPointRepository = new BreakPointHardCodedRepository();
        $this->breakPoints24 = $breakPointRepository->getBreakpointMappingForTerm(
            FeeStructureTermEnum::TwentyFourMonth
        );
        $this->breakPoints12 = $breakPointRepository->getBreakpointMappingForTerm(FeeStructureTermEnum::TwelveMonth);
        $this->strategy = new LinearRoundUpStrategy(new LinearStrategy(), new RoundUpToStrategy());
    }

    public function testFeeReturnsOnBreakpoint(): void
    {
        $loanAmount = CurrencyUtilities::getMoneyFromPence(100000);
        $expectedFee = 7000;
        $actualFee = $this->strategy->calculateFeeForLoanAmount($loanAmount, $this->breakPoints24);
        $this->assertEquals(
            $expectedFee,
            $actualFee->getAmount(),
            'The strategy should return exactly the expected fee if the requested loan amount is present in the mapping'
        );
    }

    public function testFeeReturnsLinearOffBreakpointFeeGap40(): void
    {
        $loanAmount = CurrencyUtilities::getMoneyFromPence(1150000);
        $expectedFee = 46000;
        $actualFee = $this->strategy->calculateFeeForLoanAmount($loanAmount, $this->breakPoints24);
        $this->assertEquals(
            $expectedFee,
            $actualFee->getAmount(),
            'The strategy should be able to calculate a fee using a linear calculation between breakpoints'
        );
    }

    public function testFeeReturnsLinearOffBreakpointFeeGap30(): void
    {
        $loanAmount = CurrencyUtilities::getMoneyFromPence(150000);
        $expectedFee = 8500;
        $actualFee = $this->strategy->calculateFeeForLoanAmount($loanAmount, $this->breakPoints24);
        $this->assertEquals(
            $expectedFee,
            $actualFee->getAmount(),
            'The strategy should be able to calculate a fee using a linear calculation between breakpoints'
        );
    }

    public function testFeeReturnsLinearOffBreakpointFeeGap30WithRoundUpNearestFive(): void
    {
        $loanAmount = CurrencyUtilities::getMoneyFromPence(140000);
        $expectedFee = 8500;
        $actualFee = $this->strategy->calculateFeeForLoanAmount($loanAmount, $this->breakPoints24);
        $this->assertEquals(
            $expectedFee,
            $actualFee->getAmount(),
            'The strategy should be rounding up the calculated fee to the nearest multiple of 5'
        );
    }

    public function testFeeReturnsLinearOffBreakpointFeeGap30WithRoundUpNearestFiveMultipleOfTen(): void
    {
        $loanAmount = CurrencyUtilities::getMoneyFromPence(130000);
        $expectedFee = 8000;
        $actualFee = $this->strategy->calculateFeeForLoanAmount($loanAmount, $this->breakPoints24);
        $this->assertEquals(
            $expectedFee,
            $actualFee->getAmount(),
            'The strategy should be rounding up the calculated fee to the nearest multiple of 5'
        );
    }

    public function testLinearRoundupFiveWithGivenInputForTwelveMonthFeeStructure(): void
    {
        $loanAmount = CurrencyUtilities::getMoneyFromPence(1925000);
        $expectedFee = 38500;
        $actualFee = $this->strategy->calculateFeeForLoanAmount($loanAmount, $this->breakPoints12);
        $this->assertEquals(
            $expectedFee,
            $actualFee->getAmount(),
            'The strategy should be rounding up the calculated fee to the nearest multiple of 5'
        );
    }

    /**
     * This test caught an issue where progression ratios were being rounded up to the nearest 2 dp and precision was lost
     * Testing with a linear breakpoint/fee structure is useful for testing the behavior of the algorithm as it is easy to
     * reason about the expected outputs of a given input
     * @return void
     * @throws \Exception
     */
    public function testUnorderedBreakPointsDoNotCauseException(): void
    {
        //£18,000
        $loanAmount = CurrencyUtilities::getMoneyFromPence(1800000);
        //£180
        $expectedFee = 18000;
        $unorderedBreakPointCollection = new UnorderedBreakPointCollection([
            100000 => 1000,
            300000 => 3000,
            200000 => 2000,
            2000000 => 20000,
            400000 => 4000,
        ]);
        $actualFee = $this->strategy->calculateFeeForLoanAmount($loanAmount, $unorderedBreakPointCollection);
        $this->assertEquals(
            $expectedFee,
            $actualFee->getAmount(),
            'The strategy should the breakpoints prior to running its algorithm'
        );
    }

    public function testZeroFeeBreakPointReturns(): void
    {
        //£19,000
        $loanAmount = CurrencyUtilities::getMoneyFromPence(1900000);
        //£0
        $expectedFee = 0;
        $unorderedBreakPointCollection = new UnorderedBreakPointCollection([
            100000 => 1000,
            300000 => 3000,
            200000 => 2000,
            2000000 => 20000,
            //We expect zero response
            1900000 => 0,
            400000 => 4000,
        ]);
        $actualFee = $this->strategy->calculateFeeForLoanAmount($loanAmount, $unorderedBreakPointCollection);
        $this->assertEquals(
            $expectedFee,
            $actualFee->getAmount(),
            'If the expected fee is valid, it should return, even if it is zero'
        );
    }

    public function testNegativeFeeProgression(): void
    {
        //£15,000
        $loanAmount = CurrencyUtilities::getMoneyFromPence(1500000);
        //£50
        $expectedFee = 5000;
        $unorderedBreakPointCollection = new UnorderedBreakPointCollection([
            2000000 => 0,
            1000000 => 10000,
            0 => 20000
        ]);
        $actualFee = $this->strategy->calculateFeeForLoanAmount($loanAmount, $unorderedBreakPointCollection);
        $this->assertEquals(
            $expectedFee,
            $actualFee->getAmount(),
            'Negative fee progression should be a valid linear input'
        );
    }
}