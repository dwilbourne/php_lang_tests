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
 * Class NumberFormatterPatternTest
 */

class NumberFormatterPatternTest extends TestCase {

    protected $frmtr;

    function testSetGetPattern() {

        // here's the default pattern for DECIMAL
        $this->frmtr = new \NumberFormatter('en-US', NumberFormatter::DECIMAL );
        $pat = '#,##0.###';
        $this->assertEquals($pat, $this->frmtr->getPattern());

        // set a new pattern
        $pattern = '#0';
        $this->frmtr->setPattern($pattern);
        $this->assertEquals($pattern, $this->frmtr->getPattern());

        // setPattern returns false if you put an invalid pattern in
        $badPattern = '0';
        self::assertFalse($this->frmtr->setPattern($badPattern));
        // pattern is still the old pattern, e.g. '#0'
        $this->assertEquals($pattern, $this->frmtr->getPattern());

        // here's the default pattern for PATTERN_DECIMAL
        $this->frmtr = new \NumberFormatter('en-US', NumberFormatter::PATTERN_DECIMAL);
        $pat = '#.####################################################################################################################################################################################################################################################################################################################################################';
        $this->assertEquals($pat, $this->frmtr->getPattern());
        // that's 340 '#' !!
        $this->assertEquals(340, strlen($pat) - 2);

        // here's the default pattern for CURRENCY
        $this->frmtr = new \NumberFormatter('en-US', NumberFormatter::CURRENCY);
        $pat = '¤#,##0.00';
        $this->assertEquals($pat, $this->frmtr->getPattern());

        // here's the default pattern for PERCENT
        $this->frmtr = new \NumberFormatter('en-US', NumberFormatter::PERCENT);
        $pat = '#,##0%';
        $this->assertEquals($pat, $this->frmtr->getPattern());

        // here's the default pattern for SCIENTIFIC
        $this->frmtr = new \NumberFormatter('en-US', NumberFormatter::SCIENTIFIC);
        $pat = '#E0';
        $this->assertEquals($pat, $this->frmtr->getPattern());

        // the pattern for SPELLOUT is very long and very complicated.
        //$this->frmtr = new \NumberFormatter('en-US', NumberFormatter::SPELLOUT );
        //echo $this->frmtr->getPattern() . PHP_EOL;

    }

    function testPatternsFromDifferentLocales() {
        // here's the default pattern for CURRENCY
        $this->frmtr = new \NumberFormatter('en-US', NumberFormatter::CURRENCY );
        $pat = '¤#,##0.00';
        $this->assertEquals($pat, $this->frmtr->getPattern());

        // patterns can differ between locales
        $this->frmtr = new \NumberFormatter('de-DE', NumberFormatter::CURRENCY );
        $this->assertEquals($pat, $this->frmtr->getPattern());
    }

    function testBasePattern() {

        $this->frmtr = new \NumberFormatter('en-US', NumberFormatter::DECIMAL );
        $pattern = '#,##0.###;-#,##0.###';
        $this->frmtr->setPattern($pattern);
        $expectedPattern = '#,##0.###';
        $this->assertEquals($expectedPattern, $this->frmtr->getPattern());

    }

    function testStrangeCase() {

        $this->frmtr = new \NumberFormatter('en-US', NumberFormatter::DECIMAL );
        $pattern = '#,##0.###;#,##0.###';
        $this->frmtr->setTextAttribute(\NumberFormatter::NEGATIVE_PREFIX, null);
        $this->assertEquals($pattern, $this->frmtr->getPattern());

        $pattern = '#,##0.###;-#,##0.###';
        $this->frmtr->setPattern($pattern);
        $expectedPattern = '#,##0.###';
        $this->assertEquals($expectedPattern, $this->frmtr->getPattern());

    }

}