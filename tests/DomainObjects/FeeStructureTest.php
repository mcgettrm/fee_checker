<?php

declare(strict_types=1);

namespace Lendable\Interview\Tests\DomainObjects;

use Lendable\Interview\DomainObjects\FeeStructure;
use Lendable\Interview\DomainObjects\TermBreakPointCollectionInterface;
use Lendable\Interview\Repositories\BreakPointHardCodedRepository;
use Lendable\Interview\Strategies\FeeResolutionStrategyInterface;
use Lendable\Interview\Strategies\LinearRoundUpNearestFiveStrategy;
use Lendable\Interview\Utils\FeeStructureTermEnum;
use PHPUnit\Framework\TestCase;

class FeeStructureTest extends TestCase
{
    private TermBreakPointCollectionInterface $breakPoints24;
    private TermBreakPointCollectionInterface $breakPoints12;

    public function setUp(): void
    {
        parent::setUp();
        $breakPointRepository = new BreakPointHardCodedRepository();
        $this->breakPoints24 = $breakPointRepository->getBreakpointMappingForTerm(
            FeeStructureTermEnum::TwentyFourMonth
        );
        $this->breakPoints12 = $breakPointRepository->getBreakpointMappingForTerm(FeeStructureTermEnum::TwelveMonth);
    }

    public function testFeeReturnsOnBreakpoint(): void
    {
        $loanAmount = 100000;
        $expectedFee = 7000;
        $feeStructure = new FeeStructure(
            FeeStructureTermEnum::TwentyFourMonth,
            $this->breakPoints24,
            new LinearRoundUpNearestFiveStrategy()
        );
        $actualFee = $feeStructure->getFeeForLoanAmount($loanAmount);
        $this->assertEquals(
            $expectedFee,
            $actualFee,
            'The fee structure should return exactly the expected fee if the requested loan amount is present in the mapping'
        );
    }

    public function testFeeStructureConsultsStrategy(): void
    {
        //Not actually asserting on these values, but picking something sensible anyway
        $loanAmount = 100000;
        $expectedFee = 5000;
        $strategyMock = $this->createMock(FeeResolutionStrategyInterface::class);
        $feeStructure = new FeeStructure(
            FeeStructureTermEnum::TwentyFourMonth,
            $this->breakPoints12,
            $strategyMock
        );
        $strategyMock
            ->expects($this->once())
            ->method('calculateFeeForLoanAmount')
            ->with(
                $loanAmount,
                $this->breakPoints12
            )
            ->willReturn($expectedFee);
        $feeStructure->getFeeForLoanAmount($loanAmount);
    }
}