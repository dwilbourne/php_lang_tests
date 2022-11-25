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
 * Class NumberFormatterDecimalFormatTest.
 *
 * In a nutshell,
 *
 * these attribute have only to do with formatting and nothing to do with parsing.
 *
 * setting FRACTION_DIGITS to a value is the same as setting MIN_FRACTION_DIGITS and MAX_FRACTION_DIGITS
 * to the same value
 *
 *
 */
class NumberFormatterMinMaxDigitsSignificantDigitsAndRoundingTest extends TestCase {

    protected $frmtr;

    function setUp() : void {
        $this->frmtr = new NumberFormatter('en-US', NumberFormatter::DECIMAL);
    }

    function testDefaultFractionDigits() {

        $expectedPattern = '#,##0.###';
        self::assertEquals($expectedPattern, $this->frmtr->getPattern());

        // MIN_FRACTION_DIGITS AND FRACTION_DIGITS are initialized to 0, MAX_FRACTION_DIGITS = 3
        // as you can see by the above pattern
        self::assertEquals(0, $this->frmtr->getAttribute(NumberFormatter::MIN_FRACTION_DIGITS));
        self::assertEquals(3, $this->frmtr->getAttribute(NumberFormatter::MAX_FRACTION_DIGITS));
        self::assertEquals(0, $this->frmtr->getAttribute(NumberFormatter::FRACTION_DIGITS));

    }

    function testMinMaxFractionDigitsFormatting() {

        // recall that the default for MAX_FRACTION_DIGITS is 3
        self::assertEquals(3, $this->frmtr->getAttribute(NumberFormatter::MAX_FRACTION_DIGITS));

        // now set MIN_FRACTION_DIGITS to 3 - does MAX_FRACTION_DIGITS stay the same? - yes
        $this->frmtr->setAttribute(NumberFormatter::MIN_FRACTION_DIGITS, 3);
        self::assertEquals(3, $this->frmtr->getAttribute(NumberFormatter::MAX_FRACTION_DIGITS));

        $expectedPattern = '#0.000';
        self::assertEquals($expectedPattern, $this->frmtr->getPattern());

        // what happens if I set min fraction digits to be greater than max fraction digits?
        $this->frmtr->setAttribute(NumberFormatter::MIN_FRACTION_DIGITS, 4);

        // does MAX_FRACTION_DIGITS stay the same? - NO - it is bumped up to equal min fraction digits
        self::assertEquals(4, $this->frmtr->getAttribute(NumberFormatter::MAX_FRACTION_DIGITS));

        $expectedPattern = '#0.0000';
        self::assertEquals($expectedPattern, $this->frmtr->getPattern());

        // what does a 'normal' setup look like?
        $this->frmtr->setAttribute(NumberFormatter::MIN_FRACTION_DIGITS, 2);
        // min = 2, max = 4
        $expectedPattern = '#0.00##';
        self::assertEquals($expectedPattern, $this->frmtr->getPattern());

        // FRACTION_DIGITS sets both min and max to the same number
        $this->frmtr->setAttribute(NumberFormatter::FRACTION_DIGITS, 3);
        self::assertEquals(3, $this->frmtr->getAttribute(NumberFormatter::MAX_FRACTION_DIGITS));
        self::assertEquals(3, $this->frmtr->getAttribute(NumberFormatter::MIN_FRACTION_DIGITS));
        $expectedPattern = '#0.000';
        self::assertEquals($expectedPattern, $this->frmtr->getPattern());

        // ok reset so min = 2 and max = 4
        $this->frmtr->setAttribute(NumberFormatter::MIN_FRACTION_DIGITS, 2);
        $this->frmtr->setAttribute(NumberFormatter::MAX_FRACTION_DIGITS, 4);

        // and what does that mean for formatting?
        $number = 12.1;
        $expectedResult = '12.10';
        self::assertEquals($expectedResult, $this->frmtr->format($number));

        $number = 12.1023;
        $expectedResult = '12.1023';
        self::assertEquals($expectedResult, $this->frmtr->format($number));

        // the next two are governed by rounding mode - see rounding tests below.  The default is to round TO
        // "the nearest neighbor" except when equidistant, in which case you round the to nearest even number
        // (rounding mode == HALF_EVEN), which I think is somewhat weird....

        $number = 12.10234;
        $expectedResult = '12.1023';
        self::assertEquals($expectedResult, $this->frmtr->format($number));

        $number = 12.10235;
        $expectedResult = '12.1024';
        self::assertEquals($expectedResult, $this->frmtr->format($number));
    }

    function testMinMaxIntegerDigits() {

        // now what about min / max integer digits? As far as the pattern is concerned, they sort of work
        // symmetrically on the other side of the decimal point
        $this->frmtr->setAttribute(NumberFormatter::MIN_INTEGER_DIGITS, 2);
        $expectedPattern = '#00.00##';
        self::assertEquals($expectedPattern, $this->frmtr->getPattern());

        // what is the effect on formatting?  Zero is used as the padding character for digits which
        // are not present in the input
        $number = 2.1;
        $expectedResult = '02.10';
        self::assertEquals($expectedResult, $this->frmtr->format($number));

        // what about max integer digits?  Interestingly, the pattern manipulation does not work
        // perfectly symmetrically.  Increasing max integer digits does not influence the number of '#' chars
        // as you move left - you get only one (until you start to allow the use of a grouping symbol)
        $this->frmtr->setAttribute(NumberFormatter::MAX_INTEGER_DIGITS, 4);
        $expectedPattern = '#00.00##';
        self::assertEquals($expectedPattern, $this->frmtr->getPattern());

        $this->frmtr->setAttribute(NumberFormatter::MAX_INTEGER_DIGITS, 6);
        $expectedPattern = '#00.00##';
        self::assertEquals($expectedPattern, $this->frmtr->getPattern());

        // and is there any effect on formatting?  Yes!  Even though the pattern does not visually change when increasing the
        // MAX_INTEGER_DIGITS attribute, it DOES impact the formatting behavior
        $number = 123456789.1;
        $expectedResult = '456789.10';
        self::assertEquals(6, $this->frmtr->getAttribute(NumberFormatter::MAX_INTEGER_DIGITS));
        self::assertEquals($expectedResult, $this->frmtr->format($number));

        // can you set the pattern manually and does it make any difference? The set fails....
        $pattern = '####00.00##';
        $this->frmtr->setPattern($pattern);
        $expectedPattern = '#00.00##';
        self::assertEquals($expectedPattern, $this->frmtr->getPattern());

        // what happens if we use PATTERN_DECIMAL as a style instead of DECIMAL?  Nothing - the only purpose
        // to putting additional '#' chars to the left is as placeholders for grouping
        $pattern = '####00.00##';
        $this->frmtr = new NumberFormatter('en-US', NumberFormatter::PATTERN_DECIMAL, $pattern);
        $expectedPattern = '#00.00##';
        self::assertEquals($expectedPattern, $this->frmtr->getPattern());

        $this->frmtr = new NumberFormatter('en-US', NumberFormatter::DECIMAL);
        $this->frmtr->setAttribute(NumberFormatter::GROUPING_USED, false);

        // what about setting min and max integer digits together?  Pattern will always have '#' as
        // the first character
        $this->frmtr->setAttribute(NumberFormatter::INTEGER_DIGITS, 6);
        self::assertEquals(6, $this->frmtr->getAttribute(NumberFormatter::MAX_INTEGER_DIGITS));
        self::assertEquals(6, $this->frmtr->getAttribute(NumberFormatter::MIN_INTEGER_DIGITS));
        $expectedPattern = '#000000.###';
        self::assertEquals($expectedPattern, $this->frmtr->getPattern());

        $this->frmtr->setAttribute(NumberFormatter::MIN_FRACTION_DIGITS, 2);
        $number = 123456789.1;
        $expectedResult = '456789.10';
        self::assertEquals($expectedResult, $this->frmtr->format($number));

        $number = 789.1;
        $expectedResult = '000789.10';
        self::assertEquals($expectedResult, $this->frmtr->format($number));

    }

    /**
     * @function testMinMaxFractionDigitsParsing
     *
     * what does any of this mean for parsing?  nothing!  For parsing, only the decimal point matters right now in the sense
     * that grouping separators are not allowed and the presence of a decimal point in the pattern means that
     * you can parse numbers that have decimal points in them.
     *
     */
    function testMinMaxFractionDigitsParsing() {

        $this->frmtr->setAttribute(NumberFormatter::GROUPING_USED, false);
        $this->frmtr->setAttribute(NumberFormatter::MIN_FRACTION_DIGITS, 3);
        $this->frmtr->setAttribute(NumberFormatter::MIN_INTEGER_DIGITS, 3);
        $expectedPattern = '#000.000';
        self::assertEquals($expectedPattern, $this->frmtr->getPattern());

        $input = '12.10';
        $expectedResult = 12.1;
        self::assertEquals($expectedResult, $this->frmtr->parse($input));

        $input = '12.101010105';
        $expectedResult = 12.101010105;
        self::assertEquals($expectedResult, $this->frmtr->parse($input));

        $input = '12345.101010105';
        $expectedResult = 12345.101010105;
        self::assertEquals($expectedResult, $this->frmtr->parse($input));

    }

    function testRoundingModeDefault() {
        self::assertEquals(\NumberFormatter::ROUND_HALFEVEN, $this->frmtr->getAttribute(\NumberFormatter::ROUNDING_MODE));
    }

    function testRoundingModeRoundCeilingFormatting() {

        // rounding mode set to ROUND_CEILING means that if there is any fractional value in the positions which are
        // to the right of the last value that will be formatted for output, then the last
        // value will be rounded up towards positive infinity.  This is intuitive for positive numbers
        // and less so for negative numbers.

        // ROUND_FLOOR works in exactly the opposite manner - everything is rounded towards negative infinity.

        $this->frmtr->setAttribute(\NumberFormatter::FRACTION_DIGITS, 1);
        $this->frmtr->setAttribute(\NumberFormatter::ROUNDING_MODE, \NumberFormatter::ROUND_CEILING);

        $input = '12.10';
        $expectedResult = 12.1;
        self::assertEquals($expectedResult, $this->frmtr->format($input));

        $input = '12.11';
        $expectedResult = 12.2;
        self::assertEquals($expectedResult, $this->frmtr->format($input));

        $input = '12.1000001';
        $expectedResult = 12.2;
        self::assertEquals($expectedResult, $this->frmtr->format($input));

        $input = '-12.10';
        $expectedResult = -12.1;
        self::assertEquals($expectedResult, $this->frmtr->format($input));

        $input = '-12.11';
        $expectedResult = -12.1;
        self::assertEquals($expectedResult, $this->frmtr->format($input));

        $input = '-12.1000001';
        $expectedResult = -12.1;
        self::assertEquals($expectedResult, $this->frmtr->format($input));

    }

    function testRoundingModeRoundUp() {

        // rounding mode set to ROUND_UP means that if there is any fractional value in the positions which are
        // to the right of the last value that will be formatted for output, then the last
        // value will be rounded away from zero.  This is perhaps more intuitive for negative numbers
        // than ROUND_CEILING.

        // ROUND_DOWN works in exactly the opposite manner - everything is rounded towards zero.

        $this->frmtr->setAttribute(\NumberFormatter::FRACTION_DIGITS, 1);
        $this->frmtr->setAttribute(\NumberFormatter::ROUNDING_MODE, \NumberFormatter::ROUND_UP);

        $input = '12.10';
        $expectedResult = 12.1;
        self::assertEquals($expectedResult, $this->frmtr->format($input));

        $input = '12.11';
        $expectedResult = 12.2;
        self::assertEquals($expectedResult, $this->frmtr->format($input));

        $input = '12.1000001';
        $expectedResult = 12.2;
        self::assertEquals($expectedResult, $this->frmtr->format($input));

        $input = '-12.10';
        $expectedResult = -12.1;
        self::assertEquals($expectedResult, $this->frmtr->format($input));

        $input = '-12.11';
        $expectedResult = -12.2;
        self::assertEquals($expectedResult, $this->frmtr->format($input));

        $input = '-12.1000001';
        $expectedResult = -12.2;
        self::assertEquals($expectedResult, $this->frmtr->format($input));

    }

    function testRoundingModeRoundHalfUp() {

        // rounding mode set to ROUND_UP means that if there is any fractional value in the positions which are
        // to the right of the last value that will be formatted for output, then the last
        // value will be rounded away from zero.  This is perhaps more intuitive for negative numbers
        // than ROUND_CEILING.

        // ROUND_HALFDOWN works in exactly the opposite manner - everything is rounded towards zero.

        $this->frmtr->setAttribute(\NumberFormatter::FRACTION_DIGITS, 1);
        $this->frmtr->setAttribute(\NumberFormatter::ROUNDING_MODE, \NumberFormatter::ROUND_HALFUP);

        $input = '12.10';
        $expectedResult = 12.1;
        self::assertEquals($expectedResult, $this->frmtr->format($input));

        $input = '12.1499999';
        $expectedResult = 12.1;
        self::assertEquals($expectedResult, $this->frmtr->format($input));

        $input = '12.15';
        $expectedResult = 12.2;
        self::assertEquals($expectedResult, $this->frmtr->format($input));

        $input = '-12.10';
        $expectedResult = -12.1;
        self::assertEquals($expectedResult, $this->frmtr->format($input));

        $input = '-12.1499999';
        $expectedResult = -12.1;
        self::assertEquals($expectedResult, $this->frmtr->format($input));

        $input = '-12.15';
        $expectedResult = -12.2;
        self::assertEquals($expectedResult, $this->frmtr->format($input));

    }

    function testRoundingModeRoundHalfEven() {

        // rounding mode set to ROUND_HALFEVEN means that if there is any fractional value in the positions which are
        // to the right of the last value that will be formatted for output, then the last
        // value will be rounded towards the nearest neighbor.  If the fractional value is equidistant between two
        // neighbors, the value is rounded towards the nearst even neighbor.

        $this->frmtr->setAttribute(\NumberFormatter::FRACTION_DIGITS, 1);
        $this->frmtr->setAttribute(\NumberFormatter::ROUNDING_MODE, \NumberFormatter::ROUND_HALFEVEN);

        $input = '12.10';
        $expectedResult = 12.1;
        self::assertEquals($expectedResult, $this->frmtr->format($input));

        $input = '12.1499999';
        $expectedResult = 12.1;
        self::assertEquals($expectedResult, $this->frmtr->format($input));

        $input = '12.15';
        $expectedResult = 12.2;
        self::assertEquals($expectedResult, $this->frmtr->format($input));

        $input = '-12.10';
        $expectedResult = -12.1;
        self::assertEquals($expectedResult, $this->frmtr->format($input));

        $input = '-12.1499999';
        $expectedResult = -12.1;
        self::assertEquals($expectedResult, $this->frmtr->format($input));

        $input = '-12.25';
        $expectedResult = -12.2;
        self::assertEquals($expectedResult, $this->frmtr->format($input));

    }

    function testRoundingIncrement() {

    }

    // test using significant digits instead of min/max integer / fraction digits

    function testSignificantDigits() {

    }

}