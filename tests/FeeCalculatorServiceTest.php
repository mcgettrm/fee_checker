<?php

declare(strict_types=1);

namespace Lendable\Interview\Tests;

use Lendable\Interview\DomainObjects\FeeStructureInterface;
use Lendable\Interview\Repositories\FeeStructureRepositoryInterface;
use Lendable\Interview\Services\FeeCalculatorService;
use Lendable\Interview\Utils\FeeStructureTermEnum;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class FeeCalculatorServiceTest extends TestCase
{
    private FeeCalculatorService $feeCalculatorService;
    private FeeStructureRepositoryInterface&MockObject $feeStructureRepository;

    public function setUp(): void
    {
        parent::setUp();
        $this->feeStructureRepository = $this->createMock(FeeStructureRepositoryInterface::class);
        $this->feeCalculatorService = new FeeCalculatorService($this->feeStructureRepository);
    }

    public function testServiceLoadsRequestedFeeStructureFromRepository(): void
    {
        $term = 24;
        $amount = 100000;

        $mockFeeStructure = $this->createMock(FeeStructureInterface::class);

        $fakeCalculatedFee = 100;

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

        //Asser the it returns the value that the feeStructure settled on
        $this->assertEquals($fakeCalculatedFee, $result);
    }
}