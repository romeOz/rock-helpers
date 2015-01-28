<?php

namespace rockunit;


use rock\helpers\NumericHelper;

/**
 * @group base
 * @group helpers
 */
class NumericHelperTest extends \PHPUnit_Framework_TestCase
{
    public function testParity()
    {
        $this->assertTrue(NumericHelper::isParity(2));
        $this->assertFalse(NumericHelper::isParity(3));
    }

    public function testToNumeric()
    {
        $this->assertSame(NumericHelper::toNumeric('3.14'), 3.14);
        $this->assertSame(NumericHelper::toNumeric('7'), 7);
        $this->assertSame(NumericHelper::toNumeric('foo'), 0);
    }

    public function testToPositive()
    {
        $this->assertSame(5, NumericHelper::toPositive(5));
        $this->assertSame(0, NumericHelper::toPositive(-5));
        $this->assertSame(0, NumericHelper::toPositive(-5.5));
    }

    public function testHexToBin()
    {
        $this->assertSame('example hex data', NumericHelper::hexToBin('6578616d706c65206865782064617461'));
    }
}
 