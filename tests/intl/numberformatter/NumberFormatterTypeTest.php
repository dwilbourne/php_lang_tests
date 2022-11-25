<?php
/**
 * @package: pvc
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version: 1.0
 */

namespace tests\intl\numberformatter;

use NumberFormatter;
use PHPUnit\Framework\TestCase;

class NumberFormatterTypeTest extends TestCase {

    // The locale and the type both play a role in the formulation of the pattern.  The type identifies
    // the kinds of symbols that are used to make up the format.  The locale determines the specific ordering
    // of the symbols as well as the locale-specific substitutions that are made for replacing the symbols in
    // the pattern with culturally appropriate symbols.

    // For example, in the US the decimal point sepearator is a period ('.').  in Germany, it is a comma.
    // As illustrated in this example, the pattern remains the same between the two locales, but the symbol
    // replacement is different.

    function testTypeDecimal() {
        $frmtr = new \NumberFormatter('en-US', \NumberFormatter::DECIMAL);
        $expectedResult = '#,##0.###';
        self::assertEquals($expectedResult, $frmtr->getPattern());
        $expectedResult = '1,234.567';
        self::assertEquals($expectedResult, $frmtr->format(1234.567));

        // pattern remains the same although the default symbols for decimal separator and grouping separator
        // change based on the locale
        $frmtr = new \NumberFormatter('de-DE', \NumberFormatter::DECIMAL);
        $expectedResult = '#,##0.###';
        self::assertEquals($expectedResult, $frmtr->getPattern());
        $expectedResult = '1.234,567';
        self::assertEquals($expectedResult, $frmtr->format(1234.567));

    }

    function testTypePatternDecimal() {
        $frmtr = new \NumberFormatter('en-US', \NumberFormatter::PATTERN_DECIMAL);
        $expectedResult = '#.####################################################################################################################################################################################################################################################################################################################################################';
        self::assertEquals($expectedResult, $frmtr->getPattern());
        // that is 340 '#' after the decimal point!
        self::assertEquals(342, strlen($frmtr->getPattern()));
    }

    // Here's an example where the locale not only determines a different symbol substitution but also
    // as different ordering of the symbols.

    function testTypeCurrency() {
        $frmtr = new \NumberFormatter('en-US', \NumberFormatter::CURRENCY);
        $expectedResult = '¤#,##0.00';
        self::assertEquals($expectedResult, $frmtr->getPattern());

        // pattern changes based on locale
        $frmtr = new \NumberFormatter('fr-FR', \NumberFormatter::CURRENCY);
        $expectedResult = '#,##0.00 ¤';
        self::assertEquals($expectedResult, $frmtr->getPattern());

    }

    function testTypePercent() {
        $frmtr = new \NumberFormatter('en-US', \NumberFormatter::PERCENT);
        $expectedResult = '#,##0%';
        self::assertEquals($expectedResult, $frmtr->getPattern());

        $frmtr = new \NumberFormatter('de-DE', \NumberFormatter::PERCENT);
        $expectedResult = '#,##0 %';
        self::assertEquals($expectedResult, $frmtr->getPattern());

    }
}
