<?php

namespace Lendable\Interview\Tests\Strategies;

use Lendable\Interview\Strategies\RoundUpToStrategy;
use PHPUnit\Framework\TestCase;

class RoundUpStrategyTest extends TestCase
{
    private RoundUpToStrategy $strategy;

    public function setUp(): void
    {
        parent::setUp();
        $this->strategy = new RoundUpToStrategy();
    }

    public function testRoundUpDefaultsToFive(): void
    {
        $this->assertEquals(2500, $this->strategy->round(2400));
    }

    public function testReturnsTheInputIfAlreadyMultipleOfFive(): void
    {
        $this->assertEquals(2500, $this->strategy->round(2500));
    }

    public function testHandlesFloatInput(): void
    {
        $this->assertEquals(2500, $this->strategy->round(2400.5));
    }

    public function testDoesNotRoundDown(): void
    {
        $this->assertEquals(2500, $this->strategy->round(2100));
    }
    
}