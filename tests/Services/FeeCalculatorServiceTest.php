<?php

declare(strict_types=1);

namespace Lendable\Interview\Tests\Services;

use Lendable\Interview\DomainObjects\FeeStructureInterface;
use Lendable\Interview\Repositories\BreakPointRepositoryInterface;
use Lendable\Interview\Services\FeeCalculatorService;
use Lendable\Interview\Utils\CurrencyUtilities;
use Lendable\Interview\Utils\FeeStructureTermEnum;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class FeeCalculatorServiceTest extends TestCase
{
    private FeeCalculatorService $feeCalculatorService;
    private BreakPointRepositoryInterface&MockObject $feeStructureRepository;

    public function setUp(): void
    {
        parent::setUp();
        $this->feeStructureRepository = $this->createMock(BreakPointRepositoryInterface::class);
        $this->feeCalculatorService = new FeeCalculatorService($this->feeStructureRepository);
    }

    public function testServiceLoadsRequestedFeeStructureFromRepository(): void
    {
        $term = 24;
        $amount = CurrencyUtilities::getMoneyFromPence(100000);

        $mockFeeStructure = $this->createMock(FeeStructureInterface::class);

        $fakeCalculatedFee = CurrencyUtilities::getMoneyFromPence(10000);

        $this->feeStructureRepository
            ->expects($this->once())
            ->method('getFeeStructureByTerm')
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