<?php

namespace Lendable\Interview\Tests\Strategies;

use Lendable\Interview\Strategies\LinearStrategy;
use PHPUnit\Framework\TestCase;

class LinearInterpolationStrategyTest extends TestCase
{
    private LinearStrategy $strategy;

    public function setUp(): void
    {
        parent::setUp();
        $this->strategy = new LinearStrategy();
    }

    public function testLinearInterpolationSuccessfullyIdentifiesMidpoint(): void
    {
        $this->assertEquals(150000, $this->strategy->calculateFeeBetweenBreakPoints(100000, 0.5, 200000));
    }

    public function testLinearInterpolationSuccessfullyIdentifiesUpperBound(): void
    {
        $this->assertEquals(200000, $this->strategy->calculateFeeBetweenBreakPoints(100000, 1, 200000));
    }

    public function testLinearInterpolationSuccessfullyIdentifiesLowerBound(): void
    {
        $this->assertEquals(100000, $this->strategy->calculateFeeBetweenBreakPoints(100000, 0, 200000));
    }

    public function testLinearInterpolationThrowsErrorOnNegativeProgression(): void
    {
        $this->expectException(\Exception::class);
        $this->strategy->calculateFeeBetweenBreakPoints(100000, -0.5, 200000);
    }
}