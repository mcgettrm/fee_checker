<?php

declare(strict_types=1);

namespace Lendable\Interview\Tests\Controllers;

use Lendable\Interview\Controllers\FeeCalculatorController;
use Lendable\Interview\Services\FeeCalculatorService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class FeeCalculatorControllerTest extends TestCase
{

    private FeeCalculatorController $controller;
    private FeeCalculatorService&MockObject $feeCalculatorService;

    public function setUp(): void
    {
        parent::setUp();

        $this->feeCalculatorService = $this->createMock(FeeCalculatorService::class);
        $this->controller = new FeeCalculatorController($this->feeCalculatorService);
    }

    public function testAlphabeticalAmountThrowsException(): void
    {
        $amount = "this is alphabetical";
        $term = "12";
        $this->expectException(\Exception::class);
        $this->feeCalculatorService->expects($this->never())->method('calculate');
        $this->controller->getFeeForAmountAndTerm($amount, $term);
    }

    public function testAlphabeticalTermThrowsException(): void
    {
        $amount = "1,234.89";
        $term = "this is alphabetical";
        $this->expectException(\Exception::class);
        $this->feeCalculatorService->expects($this->never())->method('calculate');
        $this->controller->getFeeForAmountAndTerm($amount, $term);
    }

    public function testFloatInputIsValid(): void
    {
        $amount = "1234.89";
        $term = "12";
        $this->feeCalculatorService
            ->expects($this->once())->method('calculate')
            ->with(123489, 12)
            ->willReturn(1000);
        $this->controller->getFeeForAmountAndTerm($amount, $term);
    }

    public function testIntegerInputIsValid(): void
    {
        $amount = "1234";
        $term = "12";
        $this->feeCalculatorService
            ->expects($this->once())->method('calculate')
            ->with(123400, 12)
            ->willReturn(1000);
        $this->controller->getFeeForAmountAndTerm($amount, $term);
    }

}