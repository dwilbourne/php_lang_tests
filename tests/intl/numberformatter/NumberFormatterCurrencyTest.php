<?php
/**
 * @package: pvc
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version: 1.0
 */

namespace tests\intl\numberformatter;

use Exception;
use NumberFormatter;
use PHPUnit\Framework\TestCase;


/**
 * Class NumberFormatterCurrencyParseTest
 */

class NumberFormatterCurrencyTest extends TestCase {

    protected $frmtr;
    protected $currencyCode;
    protected $currencySymbol;
    protected $currencyIntlSymbol;
    protected $monetarySeparator;
    protected $monetaryGroupingSeparator;
    protected $currencySymbolInPattern;

    function setUp() : void {

        $locale = 'en-US';
        $this->frmtr = new NumberFormatter($locale, NumberFormatter::CURRENCY);

        $this->currencyCode = $this->frmtr->getTextAttribute(NumberFormatter::CURRENCY_CODE);
        $this->currencySymbol = $this->frmtr->getSymbol(NumberFormatter::CURRENCY_SYMBOL);
        $this->currencyIntlSymbol = $this->frmtr->getSymbol(NumberFormatter::INTL_CURRENCY_SYMBOL);
        $this->monetarySeparator = $this->frmtr->getSymbol(NumberFormatter::MONETARY_SEPARATOR_SYMBOL);
        $this->monetaryGroupingSeparator = $this->frmtr->getSymbol(NumberFormatter::MONETARY_GROUPING_SEPARATOR_SYMBOL);

        // by the way, here is the text_unicode value for the currency symbol used in a pattern.  And there's a small bug in json_decode
        // which is requiring that the value be surrounded by double quotes
        $this->currencySymbolInPattern = json_decode('"\u00A4"'); // i.e. 'U+00A4'

    }

	/**
	 * testPatternCurrencySymbol
	 * note that the ICU documentation talks about "number skeletons", which perform the same functions as
	 * the pattern attribute in the PHP NumberFormatter.  The PHP NumberFormatter does not accept skeletons
	 * as patterns.
	 */
    function testPatternCurrencySymbol() {

        $pattern = $this->frmtr->getPattern();
        self::assertEquals('¤#,##0.00', $pattern);
        $currencySymbol = mb_substr($pattern, 0, 1);

        // note the double quotes are mandatory in order for json_decode to work right
        self::assertEquals($this->currencySymbolInPattern, $currencySymbol);
    }

    function testLocalCurrencySymbols() {
        self::assertEquals('USD', $this->currencyCode);
        self::assertEquals('$', $this->currencySymbol);
        self::assertEquals('USD', $this->currencyIntlSymbol);
        self::assertEquals('.', $this->monetarySeparator);
        self::assertEquals(',', $this->monetaryGroupingSeparator);
    }

    function testFormatPositiveNumber() {
        $expectedResult = '$1,234.56';
        self::assertEquals($expectedResult, $this->frmtr->format(1234.56));
    }

    function testFormatNegativeNumber() {
        $expectedResult = '-$1,234.56';
        self::assertEquals($expectedResult, $this->frmtr->format(-1234.56));
    }

    function testFormatInternationalSymbol() {

        // change the format to put two currency symbols in a row and you get the international currency symbol.
        // just to verify that it's the international symbol and not the currency code (which are the same in the US)
        // we will change the intl currency symbol to something different

        $pattern = $this->currencySymbolInPattern . $this->frmtr->getPattern();
        $this->frmtr->setPattern($pattern);
        self::assertEquals('¤¤#,##0.00', $pattern);

        $newSymbol = 'USDollars';
        $this->frmtr->setSymbol(NumberFormatter::INTL_CURRENCY_SYMBOL, $newSymbol);
		// \u{00A0} is non breaking space.  I develop on a windows platform and the unicode char
	    // must be in double quotes, not single quotes
        $expectedResult = "USDollars\u{00A0}1,234.56";
        self::assertEquals($expectedResult, $this->frmtr->format(1234.56));
    }

    /**
     * @function testParser
     * @param string $string
     * @param int $expectedAmt
     * @throws Exception
     *
     * @dataProvider dataProvider
     */
    function testParserBasic(string $string, $expectedAmt) {
        self::assertEquals($expectedAmt, $this->frmtr->parse($string));
    }

    function dataProvider() {
        return array(
            'basic test with $1.00' => ['$1.00', 1.00],
            'basic test without currency symbol - like a plus sign, symbol is required' => ['1.00', false],
            'OK to have the grouping separator - $1,000.00' => ['$1,000.00', 1000.00],
            'OK not to have the grouping separator also - $1000.00' => ['$1000.00', 1000.00],
            'decimal separator not necessary - $1' => ['$1', 1],
            'not necessary to have exactly 2 decimal places with the decimal separator - $1.0' => ['$1.0', 1.0],
            'not necessary to have exactly 2 decimal places with the decimal separator - $1.000' => ['$1.000', 1.000],
            'there is no rounding on fractions of a penny - $1.005' => ['$1.005', 1.005],
            'there is no rounding on fractions of a penny - $1.004' => ['$1.004', 1.004]
        );
    }

    function testParserNoCurrencySymbol() {
        // fails if there is no currency symbol
        self::assertFalse($this->frmtr->parse('2.00'));

        // trailing currency symbol no good either
        self::assertFalse($this->frmtr->parse('2.00$'));

        // trailing international currency symbol no good either
        self::assertFalse($this->frmtr->parse('2.00USD'));

    }

    function testParseIntOnly() {
        $this->frmtr->setAttribute(\NumberFormatter::PARSE_INT_ONLY, true);
        // pattern does not change
        self::assertEquals('¤#,##0.00', $this->frmtr->getPattern());
        // if you want to make it integers only, here's a way
        $newPattern = strstr($this->frmtr->getPattern(), '.', true);
        $this->frmtr->setPattern($newPattern);
        self::assertEquals('¤#,##0', $this->frmtr->getPattern());
    }

    function testSymbolPlacement() {

        // let's put the currency symbol at the end
        $currentPattern = $this->frmtr->getPattern();
        $basePattern = mb_substr($currentPattern, 1, mb_strlen($currentPattern));
        $patternWithSymbolAtEnd = $basePattern . json_decode('"'.$this->currencySymbolInPattern.'"');
        $this->frmtr->setPattern($patternWithSymbolAtEnd);

        $expectedPattern = '#,##0.00¤';
        self::assertEquals($expectedPattern, $this->frmtr->getPattern());

        // now trailing currency symbol is good
        self::assertEquals(2.00, $this->frmtr->parse('2.00$'));

        // lets allow trailing international currency symbol
        $patternWithSymbolAtEnd = $basePattern . $this->currencyIntlSymbol;
        $this->frmtr->setPattern($patternWithSymbolAtEnd);

        $expectedPattern = '#,##0.00USD';
        self::assertEquals($expectedPattern, $this->frmtr->getPattern());

        // now trailing currency symbol is good
        self::assertEquals(2.00, $this->frmtr->parse('2.00USD'));

    }
    
    function testGroupingSize() {
        $this->frmtr->setAttribute(NumberFormatter::GROUPING_SIZE, 2);
        $expectedPattern = '¤#,#0.00';
        self::assertEquals($expectedPattern, $this->frmtr->getPattern());
        
        self::assertEquals(12345.00, $this->frmtr->parse('$1,23,45.00'));    
    }



}