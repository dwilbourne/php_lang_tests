<?php
/**
 * @package: pvc
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version: 1.0
 */

namespace tests\intl\numberformatter;


use NumberFormatter;
use PHPUnit\Framework\TestCase;

/**
 * Class NumberFormatterIntegerParseTest
 */

class NumberFormatterIntegerTest extends TestCase {

    protected $frmtr;

    function setUp() : void {
        $this->frmtr = new NumberFormatter('en-US', NumberFormatter::PATTERN_DECIMAL );
    }

    function testIntegerParseBasic() {

        // phpinfo(INFO_MODULES);
        // on my system reveals ICU version 57.1 and ICU Data version 57.1
        //
        // can also use php -i | find "ICU" from the command line

        // most basic test
        $pos = 0;
        $expectedResult = 12345;
        self::assertEquals($expectedResult, $this->frmtr->parse('12345', NumberFormatter::TYPE_INT64, $pos));
        self::assertEquals(5, $pos);

        // handles a negative sign
        $pos = 0;
        $expectedResult = -12345;
        self::assertEquals($expectedResult, $this->frmtr->parse('-12345', NumberFormatter::TYPE_INT64, $pos));
        self::assertEquals(6, $pos);

        // parser stops at the 'K'
        $pos = 0;
        $expectedResult = 123;
        self::assertEquals($expectedResult, $this->frmtr->parse('123K4', NumberFormatter::TYPE_INT64, $pos));
        self::assertEquals(3, $pos);

        // parser does not stop at the decimal point
        $pos = 0;
        $expectedResult = 123;
        self::assertEquals($expectedResult, $this->frmtr->parse('123.9549', NumberFormatter::TYPE_INT64, $pos));
        self::assertEquals(8, $pos);

    }

    function testAffirmationNegation() {

        // fails on a plus sign
        $pos = 0;
        $expectedResult = 12345;
        $this->assertFalse($this->frmtr->parse('+12345', NumberFormatter::TYPE_INT64, $pos));
        // note that the parser failed on the first character of the string so the parsing position remains at 0.
        self::assertEquals(0, $pos);

        // a trailing negative sign is not recognized - need a special pattern for that
        $pos = 0;
        $expectedResult = 12345;
        self::assertEquals($expectedResult, $this->frmtr->parse('12345-', NumberFormatter::TYPE_INT64, $pos));
        // although the parse returned a number, $pos is still 5, indicating the parser stopped short of the end of the string.
        // also note - if $pos never gets past 0, the formatter will return false.  If $pos is greater than 0, then
        // the formatter returns what it what able to parse, which is not necessarily the whole string
        self::assertEquals(5, $pos);

        // a trailing plus sign is not recognized either
        $pos = 0;
        $expectedResult = 12345;
        self::assertEquals($expectedResult, $this->frmtr->parse('12345+', NumberFormatter::TYPE_INT64, $pos));
        // although the parse returned a number, $pos is still 5, indicating the parser stopped short of the end of the string
        self::assertEquals(5, $pos);

    }

    function testPatternMakesNoDifferenceForPureNumerics() {

        // here's the default pattern for PATTERN_DECIMAL
        $pattern = '#.####################################################################################################################################################################################################################################################################################################################################################';
        self::assertEquals($pattern, $this->frmtr->getPattern());

        // change it to 'an integer pattern'
        $pattern = '#0';
        $this->frmtr->setPattern($pattern);
        self::assertEquals($pattern, $this->frmtr->getPattern());

        // try to parse a float
        // no decimal separator or fractional digits in the pattern, but no problems parsing them
        $pos = 0;
        $expectedResult = 123.456;
        self::assertEquals($expectedResult, $this->frmtr->parse('123.456', NumberFormatter::TYPE_DOUBLE, $pos));
        self::assertEquals(7, $pos);

        // set the PARSE_INT_ONLY attribute
        $this->frmtr->setAttribute(NumberFormatter::PARSE_INT_ONLY, true);
        $pos = 0;
        $expectedResult = 123;
        self::assertEquals($expectedResult, $this->frmtr->parse('123.456', NumberFormatter::TYPE_DOUBLE, $pos));
        self::assertEquals(3, $pos);

    }


    function testParseIntOnly() {

        // parse only the integer portion of a number and do not advance the parsing position past the decimal point
        $this->frmtr->setAttribute(NumberFormatter::PARSE_INT_ONLY, true);
        $pos = 0;
        $expectedResult = 1234;
        self::assertEquals($expectedResult, $this->frmtr->parse('1234.56', NumberFormatter::TYPE_INT64, $pos));
        self::assertEquals(4, $pos);
    }

    function testLargeSmallNumbers() {
        $pos = 0;
        // this is the value of PHP_INT_MAX on my 64 bit Windows system
        $max_int = '9223372036854775807';
        $expectedResult = 9223372036854775807;
        self::assertEquals($expectedResult, $this->frmtr->parse($max_int, NumberFormatter::TYPE_INT64, $pos));

        // add one to it and see what happens in the parse.  The return type is supposed to be an integer
        $max_int_plus_one = '9223372036854775808';
        // expected result is typed as a double when things get too big
        $expectedResult = 9223372036854775808;
        self::assertEquals('double', gettype($expectedResult));

        // fails if you tell it to return an integer
        $pos = 0;
        $this->assertFalse($this->frmtr->parse($max_int_plus_one, NumberFormatter::TYPE_INT64, $pos));
        // out of curiosity, where is $pos now? As far down the string as it got: 19 chars
        self::assertEquals(19, $pos);

        // succeeds if you tell it to return a double
        $pos = 0;
        self::assertEquals($expectedResult, $this->frmtr->parse($max_int_plus_one, NumberFormatter::TYPE_DOUBLE, $pos));

        // this is the value of PHP_INT_MIN on my 64 bit Windows system
        $min_int = '-9223372036854775808';
        $expectedResult = -9223372036854775808;
        $pos = 0;
        self::assertEquals($expectedResult, $this->frmtr->parse($min_int, NumberFormatter::TYPE_INT64, $pos));

        $min_int_minus_one = '-9223372036854775809';
        // now typed as a double
        $expectedResult = -9223372036854775809;
        self::assertEquals('double', gettype($expectedResult));

        // fails if you tell it to return an integer
        $pos = 0;
        $this->assertFalse($this->frmtr->parse($min_int_minus_one, NumberFormatter::TYPE_INT64, $pos));
        // out of curiosity, where is $pos now? As far down the string as it got: 20 chars
        self::assertEquals(20, $pos);

            // succeeds if you tell it to return a double
        $pos = 0;
        self::assertEquals($expectedResult, $this->frmtr->parse($min_int_minus_one, NumberFormatter::TYPE_DOUBLE, $pos));

    }

}