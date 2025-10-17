<?php

declare(strict_types=1);

namespace Lendable\Interview\Tests;

use PHPUnit\Framework\TestCase;

class CalculateFeeIntegrationTest extends TestCase
{
    private string $binaryLocation;

    public function setUp(): void
    {
        parent::setUp();
        $this->binaryLocation = __DIR__ . DIRECTORY_SEPARATOR . '../bin/calculate-fee';
    }

    public function testRequiresTwoArguments(): void
    {
        exec("php {$this->binaryLocation}", $output, $exitCode);
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
        exec("php {$this->binaryLocation} --2000 --24", $output, $exitCode);
        $this->assertEquals(
            0,
            $exitCode,
            "Expected code zero (success) but received exit code: {$exitCode}\n Output:\n" . \implode("\n", $output)
        );
    }

    public function testErrorsOutputToSTDERR(): void
    {
        //We need to assert specifically that we are getting a message from STDERR
        //Maybe a bit hacky but we'll call the binary twice here, once piping STDERR to STDOUT and once listening only for STDOUT
        //Then we can assert that the target string is only present in the STDERR to know that we got it from there specifically
        $errorOutputString = "Error:";

        //Invalid because no arguments are provided
        $withStdErrOutput = shell_exec("php {$this->binaryLocation} 2>&1");

        $this->assertStringContainsString(
            $errorOutputString,
            $withStdErrOutput,
            "Amount error message missing. The output was: \n" . $withStdErrOutput
        );

        //Invalid because no arguments are provided
        $standardOutputOnly = shell_exec("php {$this->binaryLocation}");

        $this->assertStringNotContainsString(
            $errorOutputString,
            $standardOutputOnly,
            'The standard output should not contain the stdErr output'
        );
    }
}