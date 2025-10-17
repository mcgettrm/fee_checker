<?php

declare(strict_types=1);

namespace Lendable\Interview\Tests;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class CalculateFeeIntegrationTest extends TestCase
{
    public function testRequiresTwoArguments(): void
    {
        exec('php ./bin/calculate-fee', $output, $exitCode);
        $this->assertNotEquals(0, $exitCode);
        $this->assertStringContainsString('You must provide a term', implode("\n", $output));
    }

    #[Test]
    public function itAddsNumbers(): void
    {
        $this->assertSame(2, 1 + 1);
    }
}