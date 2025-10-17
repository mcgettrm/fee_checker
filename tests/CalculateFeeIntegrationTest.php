<?php

declare(strict_types=1);

namespace Lendable\Interview\Tests;

use PHPUnit\Framework\TestCase;

class CalculateFeeIntegrationTest extends TestCase
{
    public function testRequiresTwoArguments(): void
    {
        exec('php ../bin/calculate-fee', $output, $exitCode);
        $this->assertEquals(
            1,
            $exitCode,
            "Expected code 1 but received exit code: {$exitCode}\n Output:\n" . \implode("\n", $output)
        );
        $this->assertStringContainsString(
            'You must provide a term',
            implode("\n", $output),
            "Term error message missing. The output was: \n" . \implode("\n", $output)
        );
        $this->assertStringContainsString(
            'You must provide an amount',
            implode("\n", $output),
            "Amount error message missing. The output was: \n" . \implode("\n", $output)
        );
    }

    public function testPassingBothArgumentsReturnsSuccess(): void
    {
        exec('php ../bin/calculate-fee --2000 --24', $output, $exitCode);
        $this->assertEquals(
            0,
            $exitCode,
            "Expected code zero (success) but received exit code: {$exitCode}\n Output:\n" . \implode("\n", $output)
        );
    }
}