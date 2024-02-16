<?php
/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */

namespace tests\intl\numberformatter;


use NumberFormatter;
use PHPUnit\Framework\TestCase;

/**
 * Class NumberFormatterFloatParseTest
 */
class NumberFormatterDecimalTest extends TestCase {

    protected $frmtr;

    function setUp() : void {
        $this->frmtr = new \NumberFormatter('en-US', NumberFormatter::PATTERN_DECIMAL );
    }

    function testFloatParse() {

        // one significant digit
        $pattern = '0.0';
        $this->frmtr->setPattern($pattern);
        $this->assertEquals($pattern, $this->frmtr->getPattern());

        // most basic test
        $pos = 0;
        $expectedResult = 12345.0;
        $this->assertEquals($expectedResult, $this->frmtr->parse('12345.0', NumberFormatter::TYPE_DOUBLE, $pos));
        $this->assertEquals(7, $pos);

        $pos = 0;
        $expectedResult = 123;
        $this->assertEquals($expectedResult, $this->frmtr->parse('123K4', NumberFormatter::TYPE_DOUBLE, $pos));
        // parsing stops correctly at position 3
        $this->assertEquals(3, $pos);


        // is the decimal point required? no
        $pos = 0;
        $expectedResult = 12345.0;
        $this->assertEquals($expectedResult, $this->frmtr->parse('12345', NumberFormatter::TYPE_DOUBLE, $pos));
        $this->assertEquals(5, $pos);

        // is the first digit after the decimal point required? no
        $pos = 0;
        $expectedResult = 12345.0;
        $this->assertEquals($expectedResult, $this->frmtr->parse('12345.', NumberFormatter::TYPE_DOUBLE, $pos));
        $this->assertEquals(6, $pos);

        // fraction digits does not impact parsing
        $pos = 0;
        $expectedResult = 12345.0;
        $this->frmtr->setAttribute(NumberFormatter::FRACTION_DIGITS, 1);
        $this->assertEquals($expectedResult, $this->frmtr->parse('12345.', NumberFormatter::TYPE_DOUBLE, $pos));
        $this->assertEquals(6, $pos);


        $pos = 0;
        $expectedResult = 12345.087;
        $this->assertEquals($expectedResult, $this->frmtr->parse('12345.087', NumberFormatter::TYPE_DOUBLE, $pos));
        $this->assertEquals(9, $pos);

        // nor does min fraction digits

        $pos = 0;
        $expectedResult = 12345.0;
        $this->frmtr->setAttribute(NumberFormatter::MIN_FRACTION_DIGITS, 1);
        $this->assertEquals($expectedResult, $this->frmtr->parse('12345.', NumberFormatter::TYPE_DOUBLE, $pos));
        $this->assertEquals(6, $pos);

        // what about'vice-versa', i.e. a pattern without any decimal - will it parse a float?
        $pattern = '0';
        $this->frmtr->setPattern($pattern);
        $this->assertEquals($pattern, $this->frmtr->getPattern());

        // most basic test - wow it works! decimal point and trailing '#' are not necessary for parsing!
        $pos = 0;
        $expectedResult = 12345.1;
        $this->assertEquals($expectedResult, $this->frmtr->parse('12345.1', NumberFormatter::TYPE_DOUBLE, $pos));
        $this->assertEquals(7, $pos);

    }

    function testPrecisionRequirement() {

        // demonstrates the parser will not tolerate 2 decimal points
        $pos = 0;
        $input = '12345.087.921';
        $expectedResult = 12345.087;
        $this->assertEquals($expectedResult, $this->frmtr->parse($input, NumberFormatter::TYPE_DOUBLE, $pos));
        $this->assertEquals(9, $pos);

        // therefore a good solution for requiring a certain degree of precision in the input is to parse it and,
        // assuming it parses correctly as a float, locate the decimal point and then count the number of digits afterwards

        $pos = 0;
        $input = '12345.087';
        $expectedResult = 12345.087;
        $this->assertEquals($expectedResult, $this->frmtr->parse($input, NumberFormatter::TYPE_DOUBLE, $pos));

        $sep = $this->frmtr->getSymbol(NumberFormatter::DECIMAL_SEPARATOR_SYMBOL);
        $precision = mb_strlen($input) - mb_strpos($input, $sep) - 1;
        $this->assertEquals(3, $precision);

    }

}