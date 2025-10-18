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
    }

    public function testPassingBothArgumentsReturnsSuccess(): void
    {
        exec("php {$this->binaryLocation} 2000 24", $output, $exitCode);
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
            (string)$withStdErrOutput,
            "Amount error message missing. The output was: \n" . $withStdErrOutput
        );

        //Invalid because no arguments are provided
        $standardOutputOnly = shell_exec("php {$this->binaryLocation}");

        $this->assertStringNotContainsString(
            $errorOutputString,
            (string)$standardOutputOnly,
            'The standard output should not contain the stdErr output'
        );
    }

    public function testExampleInputOne(): void
    {
        $loanAmount = '11,500.00';
        $term = '24';
        $expectedFee = '460.00';
        exec("php {$this->binaryLocation} {$loanAmount} {$term}", $output);
        $implodedOutput = implode("\n", $output);
        $this->assertStringContainsString(
            $expectedFee,
            $implodedOutput,
            "A term of {$term} and a loan of {$loanAmount} should have returned a value of {$expectedFee} but instead we got \n Output:\n" . $implodedOutput
        );
    }

    public function testExampleInputTwo(): void
    {
        $loanAmount = '19,250.00';
        $term = '12';
        $expectedFee = '385.00';
        exec("php {$this->binaryLocation} {$loanAmount} {$term}", $output);
        $implodedOutput = implode("\n", $output);
        $this->assertStringContainsString(
            $expectedFee,
            $implodedOutput,
            "A term of {$term} and a loan of {$loanAmount} should have returned a value of {$expectedFee} but instead we got \n Output:\n" . $implodedOutput
        );
    }

    public function testBreachingUpperLoanAmountCausesErrorExistCode(): void
    {
        $loanAmount = '20,000.01';
        $term = '12';
        exec("php {$this->binaryLocation} {$loanAmount} {$term}", $output, $exitCode);
        $this->assertEquals(1, $exitCode);
        $this->assertEmpty($output);
    }

    public function testBreachingLowerLoanAmountCausesErrorExistCode(): void
    {
        $loanAmount = '999.99';
        $term = '12';
        exec("php {$this->binaryLocation} {$loanAmount} {$term}", $output, $exitCode);
        $this->assertEquals(1, $exitCode);
        $this->assertEmpty($output);
    }

    public function testUpperLoanAmountBoundaryIsInclusiveEdgeCase(): void
    {
        $loanAmount = '20,000.00';
        $term = '12';
        exec("php {$this->binaryLocation} {$loanAmount} {$term}", $output, $exitCode);
        $this->assertEquals(0, $exitCode);
    }

    public function testLowerLoanAmountBoundaryIsInclusiveEdgeCase(): void
    {
        $loanAmount = '1000.00';
        $term = '12';
        exec("php {$this->binaryLocation} {$loanAmount} {$term}", $output, $exitCode);
        $this->assertEquals(0, $exitCode);
    }

    public function testTermLengthOf15IsInvalid(): void
    {
        $loanAmount = '10,000.00';
        $term = '15';
        exec("php {$this->binaryLocation} {$loanAmount} {$term}", $output, $exitCode);
        $this->assertEquals(
            1,
            $exitCode,
            'Only term limits of 12 and 24 are valid. Per the docs, we can assume all inputs will be of these term limits'
        );
    }

    /**
     * This test caught an issue where the use of modulo implicitly reduced precision
     * @return void
     */
    public function testInputHandlesPence(): void
    {
        $loanAmount = '1,300.56';
        $term = '12';
        exec("php {$this->binaryLocation} {$loanAmount} {$term}", $output, $exitCode);
        $this->assertEquals(
            0,
            $exitCode,
            'Input must handle pence'
        );
        $this->assertEquals('65.00', implode($output), 'The output should only show 65.00');
    }
}