<?php
/**
 * @package: pvc
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version: 1.0
 */

namespace tests\intl\numberformatter;

use PHPUnit\Framework\TestCase;

class NumberFormatterPercentTest extends TestCase {

    protected $frmtr;

    function setUp() : void {
        $this->frmtr = new \NumberFormatter('en-US', \NumberFormatter::PERCENT);
    }

    function testPattern() {
        $expectedResult = '#,##0%';
        self::assertEquals($expectedResult, $this->frmtr->getPattern());
    }

    function testParse() {
        $expectedResult = 0.05;
        self::assertEquals($expectedResult, $this->frmtr->parse('5%'));

        $expectedResult = 50;
        self::assertEquals($expectedResult, $this->frmtr->parse('5,000%'));
        self::assertEquals($expectedResult, $this->frmtr->parse('5000%'));

        $expectedResult = false;
        self::assertEquals($expectedResult, $this->frmtr->parse('50,00%'));

    }

    function testSymbol() {
        $symbol = '&';
        $this->frmtr->setSymbol(\NumberFormatter::PERCENT_SYMBOL, $symbol);
        $expectedResult = 0.05;
        self::assertEquals($expectedResult, $this->frmtr->parse('5&'));
    }

    function testPerMillSymbol() {
        $symbol = '‰';
        self::assertEquals($symbol, $this->frmtr->getSymbol(\NumberFormatter::PERMILL_SYMBOL));
        $pattern = '#,##0' . $symbol;
        self::assertTrue($this->frmtr->setPattern($pattern));
        $input = '5' . $symbol;
        $expectedResult = 0.005;
        self::assertEquals($expectedResult, $this->frmtr->parse($input));
    }

}
