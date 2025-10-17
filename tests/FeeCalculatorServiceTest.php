<?php

namespace Lendable\Interview\Tests;

use Lendable\Interview\FeeCalculatorService;
use PHPUnit\Framework\TestCase;

class FeeCalculatorServiceTest extends TestCase
{
    public function testServiceLoadsRequestedFeeStructureFromRepository()
    {
        $service = new FeeCalculatorService();
    }
}