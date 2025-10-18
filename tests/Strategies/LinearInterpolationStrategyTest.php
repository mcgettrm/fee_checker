<?php

namespace Lendable\Interview\Tests\Strategies;

use Lendable\Interview\Strategies\LinearStrategy;
use PHPUnit\Framework\TestCase;

use function PHPUnit\Framework\assertEquals;

class LinearInterpolationStrategyTest extends TestCase
{
    private LinearStrategy $strategy;

    public function setUp(): void
    {
        parent::setUp();
        $this->strategy = new LinearStrategy();
    }

    public function testLinearInterpolationSuccessfullyIdentifiesMidpoint()
    {
        assertEquals(150000, $this->strategy->calculateFeeBetweenBreakPoints(100000, 0.5, 200000));
    }

    public function testLinearInterpolationSuccessfullyIdentifiesUpperBound()
    {
        assertEquals(200000, $this->strategy->calculateFeeBetweenBreakPoints(100000, 1, 200000));
    }

    public function testLinearInterpolationSuccessfullyIdentifiesLowerBound()
    {
        assertEquals(100000, $this->strategy->calculateFeeBetweenBreakPoints(100000, 0, 200000));
    }
}