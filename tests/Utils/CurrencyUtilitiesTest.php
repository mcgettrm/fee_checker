<?php

namespace Lendable\Interview\Tests\Utils;

use Lendable\Interview\Utils\CurrencyUtilities;
use PHPUnit\Framework\TestCase;

class CurrencyUtilitiesTest extends TestCase
{
    public function testExampleInputOneReturnsAsInteger()
    {
        $input = '11,500.00';
        $this->assertEquals(
            1150000,
            CurrencyUtilities::convertStringToPence($input),
            'The first example string did not convert correctly to an integer'
        );
    }

    public function testExampleInputTwoReturnsAsInteger()
    {
        $input = '19,250.00';
        $this->assertEquals(
            1925000,
            CurrencyUtilities::convertStringToPence($input),
            'The second example string did not convert correctly to an integer'
        );
    }

    public function testCanGenerateExpectedOutputFromPenceValue()
    {
        $input = 1925000;
        $this->assertEquals(
            '19,250.00',
            CurrencyUtilities::convertPenceToDisplay($input),
        );
    }

    public function testCurrencyStringToIntHandlesDecimals()
    {
        $input = '19,250.55';
        $this->assertEquals(
            1925055,
            CurrencyUtilities::convertStringToPence($input)
        );
    }

    public function testCurrencyIntegerToDisplayHandlesDecimals()
    {
        $input = 1925055;
        $this->assertEquals(
            '19,250.55',
            CurrencyUtilities::convertPenceToDisplay($input)
        );
    }
}