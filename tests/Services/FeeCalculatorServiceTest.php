<?php

declare(strict_types=1);

namespace Lendable\Interview\Tests\Services;

use Lendable\Interview\DomainObjects\BreakPointCollection;
use Lendable\Interview\DomainObjects\FeeStructureInterface;
use Lendable\Interview\Factories\FeeStructureFactoryInterface;
use Lendable\Interview\Repositories\BreakPointRepositoryInterface;
use Lendable\Interview\Services\FeeCalculatorService;
use Lendable\Interview\Utils\CurrencyUtilities;
use Lendable\Interview\Utils\FeeStructureTermEnum;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class FeeCalculatorServiceTest extends TestCase
{
    private FeeCalculatorService $feeCalculatorService;
    private BreakPointRepositoryInterface&MockObject $breakPointRepository;

    private FeeStructureFactoryInterface&MockObject $feeStructureFactory;

    public function setUp(): void
    {
        parent::setUp();
        $this->breakPointRepository = $this->createMock(BreakPointRepositoryInterface::class);
        $this->feeStructureFactory = $this->createMock(FeeStructureFactoryInterface::class);
        $this->feeCalculatorService = new FeeCalculatorService(
            $this->breakPointRepository,
            $this->feeStructureFactory,
        );
    }

    public function testServiceThrowsAnExceptionOnInvalidTerm(): void
    {
        $amount = CurrencyUtilities::getMoneyFromPence(100000);
        $this->expectException(\Exception::class);
        $this->feeCalculatorService->calculate($amount, 55);
    }

    public function testServiceLoadsRequestedFeeStructureFromFactory(): void
    {
        $term = 24;
        $amount = CurrencyUtilities::getMoneyFromPence(100000);

        $mockFeeStructure = $this->createMock(FeeStructureInterface::class);
        $fakeBreakPointCollection = new BreakPointCollection([]);
        $this->breakPointRepository
            ->expects($this->once())
            ->method('getBreakpointMappingForTerm')
            ->with(FeeStructureTermEnum::tryFrom($term))
            ->willReturn($fakeBreakPointCollection);

        $fakeCalculatedFee = CurrencyUtilities::getMoneyFromPence(10000);

        $this->feeStructureFactory
            ->expects($this->once())
            ->method('getFeeStructure')
            ->with(FeeStructureTermEnum::tryFrom($term))
            ->willReturn($mockFeeStructure);
        $mockFeeStructure
            ->expects($this->once())
            ->method('getFeeForLoanAmount')
            ->with($amount)
            ->willReturn($fakeCalculatedFee);

        $result = $this->feeCalculatorService->calculate($amount, $term);

        //Assert thT it returns the value that the feeStructure settled on
        $this->assertEquals($fakeCalculatedFee->getAmount(), $result->getAmount());
    }
}