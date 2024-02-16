<?php
/**
 * @package: pvc
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version: 1.0
 */

namespace tests\intl\numberformatter;

use NumberFormatter;
use PHPUnit\Framework\TestCase;

class NumberFormatterGroupingTest extends TestCase {

    protected $frmtr;

    function setUp() : void {
        $this->frmtr = new NumberFormatter('en-US', NumberFormatter::DECIMAL );
    }

    function testSetGetGroupingUsed() {
        $expectedPattern = '#,##0.###';
        self::assertEquals($expectedPattern, $this->frmtr->getPattern());

        // what is the value of the GROUPING_USED attribute? integer value 1
        $expectedValue = 1;
        self::assertEquals($expectedValue, $this->frmtr->getAttribute(\NumberFormatter::GROUPING_USED));

        // now turn grouping used off
        $this->frmtr->setAttribute(\NumberFormatter::GROUPING_USED, false);
        $expectedPattern = '0.###';
        self::assertEquals($expectedPattern, $this->frmtr->getPattern());

        // what is the value of the GROUPING_USED attribute? integer value 0
        $expectedValue = 0;
        self::assertEquals($expectedValue, $this->frmtr->getAttribute(\NumberFormatter::GROUPING_USED));

        // now set max fraction digits to zero
        $this->frmtr->setAttribute(NumberFormatter::MAX_FRACTION_DIGITS, 0);
        self::assertEquals('0', $this->frmtr->getPattern());

        // now if we turn grouping back on, do the factional digits reappear in the pattern? They do not!
        $this->frmtr->setAttribute(\NumberFormatter::GROUPING_USED, true);
        self::assertEquals('#,##0', $this->frmtr->getPattern());
    }

    function testGroupingSizeAndSeparators() {

        $pattern = '#0';
        $this->frmtr->setPattern($pattern);
        self::assertEquals($pattern, $this->frmtr->getPattern());

        // try to parse a number with a grouping separator, but the grouping separator is not recognized.  Contrast this with
        // the decimal separator, where the above pattern would still parse a number like 123.456 with no issue.  In other words,
        // the decimal separator's presence is not required to parse a fractional number but the grouping separator is required
        // in the pattern in order to parse a number that contains a grouping separator.
        $pos = 0;
        $expectedResult = 123;
        self::assertEquals($expectedResult, $this->frmtr->parse('123,456', NumberFormatter::TYPE_INT64, $pos));
        self::assertEquals(3, $pos);


        // here's a pattern sort of like the default pattern for DECIMAL but without the
        // decimal separator and the fractional digits
        $pattern = '#,##0';
        $this->frmtr->setPattern($pattern);
        self::assertEquals($pattern, $this->frmtr->getPattern());

        // now it parses the grouping separator
        $pos = 0;
        $expectedResult = 123456;
        self::assertEquals($expectedResult, $this->frmtr->parse('123,456', NumberFormatter::TYPE_INT64, $pos));
        self::assertEquals(7, $pos);

        // note that the grouping separator is considered optional at this point
        $pos = 0;
        $expectedResult = 123456;
        self::assertEquals($expectedResult, $this->frmtr->parse('123456', NumberFormatter::TYPE_INT64, $pos));
        self::assertEquals(6, $pos);

        // you cannot put the grouping separator just anywhere in the input (without LENIENT_PARSE set)
        $pos = 0;
        $this->assertFalse($this->frmtr->parse('1234,56', NumberFormatter::TYPE_INT64, $pos));

        // let's try changing the pattern manually
        $pattern = '#,#0';
        $this->frmtr->setPattern($pattern);
        self::assertEquals($pattern, $this->frmtr->getPattern());

        $pos = 0;
        $this->assertEquals(123456, $this->frmtr->parse('12,34,56', NumberFormatter::TYPE_INT64, $pos));

        $this->frmtr->setAttribute(NumberFormatter::GROUPING_SIZE, 4);
        // changing the grouping size changes the pattern
        $pattern = '#,###0';
        self::assertEquals($pattern, $this->frmtr->getPattern());

        $pos = 0;
        $expectedResult = 12345678;
        self::assertEquals($expectedResult, $this->frmtr->parse('1234,5678', NumberFormatter::TYPE_INT64, $pos));
        self::assertEquals(9, $pos);

        // you can also change the symbol for the grouping separator.  So now it is easy to see that each locale
        // has its own values for grouping size, grouping separator and decimal separator
        $this->frmtr->setSymbol(NumberFormatter::GROUPING_SEPARATOR_SYMBOL, '*');
        $pos = 0;
        $expectedResult = 12345678;
        self::assertEquals($expectedResult, $this->frmtr->parse('1234*5678', NumberFormatter::TYPE_INT64, $pos));
        self::assertEquals(9, $pos);

        // let's set the grouping symbol back to the US standard
        $this->frmtr->setSymbol(NumberFormatter::GROUPING_SEPARATOR_SYMBOL, ',');


        // let's play with the grouping size some more
        $this->assertTrue($this->frmtr->setAttribute(NumberFormatter::GROUPING_SIZE, -1));
        // evidently what actually gets set is 0 if the grouping size is not within range
        // unfortunately it does not raise an exception or return false on the set
        self::assertEquals(0, $this->frmtr->getAttribute(NumberFormatter::GROUPING_SIZE));

        // is there an upper limit on the grouping size?  Yes, things get funky after size > pow(2, 30) on
        // my system.
        $maxGroupingSize = pow(2, 30);
        $this->assertTrue($this->frmtr->setAttribute(NumberFormatter::GROUPING_SIZE, $maxGroupingSize));
        self::assertEquals($maxGroupingSize, $this->frmtr->getAttribute(NumberFormatter::GROUPING_SIZE));

        $maxGroupingSize = pow(2, 31);
        $this->assertTrue($this->frmtr->setAttribute(NumberFormatter::GROUPING_SIZE, $maxGroupingSize));
        // flips the sign on pow(2, 31)!
        self::assertEquals(-$maxGroupingSize, $this->frmtr->getAttribute(NumberFormatter::GROUPING_SIZE));


        $maxGroupingSize = pow(2, 32);
        $this->assertTrue($this->frmtr->setAttribute(NumberFormatter::GROUPING_SIZE, $maxGroupingSize));
        // sets the value to 0 on pow(2, 32) and greater
        self::assertEquals(0, $this->frmtr->getAttribute(NumberFormatter::GROUPING_SIZE));

        // let's verify the same for secondary grouping size
        $this->assertTrue($this->frmtr->setAttribute(NumberFormatter::SECONDARY_GROUPING_SIZE, -1));
        // evidently what actually gets set is 0 if the grouping size is not within range
        // unfortunately it does not raise an exception or return false on the set
        self::assertEquals(0, $this->frmtr->getAttribute(NumberFormatter::SECONDARY_GROUPING_SIZE));

        // is there an upper limit on the grouping size?  Yes, things get funky after size > pow(2, 30) on
        // my system.
        $maxGroupingSize = pow(2, 30);
        $this->assertTrue($this->frmtr->setAttribute(NumberFormatter::SECONDARY_GROUPING_SIZE, $maxGroupingSize));
        self::assertEquals($maxGroupingSize, $this->frmtr->getAttribute(NumberFormatter::SECONDARY_GROUPING_SIZE));

        $maxGroupingSize = pow(2, 31);
        $this->assertTrue($this->frmtr->setAttribute(NumberFormatter::SECONDARY_GROUPING_SIZE, $maxGroupingSize));
        // flips the sign on pow(2, 31)!
        self::assertEquals(-$maxGroupingSize, $this->frmtr->getAttribute(NumberFormatter::SECONDARY_GROUPING_SIZE));


        $maxGroupingSize = pow(2, 32);
        $this->assertTrue($this->frmtr->setAttribute(NumberFormatter::SECONDARY_GROUPING_SIZE, $maxGroupingSize));
        // sets the value to 0 on pow(2, 32) and greater
        self::assertEquals(0, $this->frmtr->getAttribute(NumberFormatter::SECONDARY_GROUPING_SIZE));

        // what does the pattern look like if the secondary grouping size is set?
        $secondaryGroupingSize = 2;
        $groupingSize = 3;
        $this->assertTrue($this->frmtr->setAttribute(NumberFormatter::GROUPING_SIZE, $groupingSize));
        $this->assertTrue($this->frmtr->setAttribute(NumberFormatter::SECONDARY_GROUPING_SIZE, $secondaryGroupingSize));
        $expectedPattern = '#,##,##0';
        self::assertEquals($expectedPattern, $this->frmtr->getPattern());

    }



    function testLenientStrictParse() {

        $pattern = '#,##0';
        $this->frmtr->setPattern($pattern);
        self::assertEquals($pattern, $this->frmtr->getPattern());

        // the default value for the attribute is 0 (not a strict false)
        self::assertEquals(0, $this->frmtr->getAttribute(NumberFormatter::LENIENT_PARSE));

        // recall that parsing a number with the grouping separator in the wrong place fails
        $pos = 0;
        $this->assertFalse($this->frmtr->parse('1234,56', NumberFormatter::TYPE_INT64, $pos));

        // let's try lenient parse
        $this->frmtr->setAttribute(NumberFormatter::LENIENT_PARSE, 2);
        // note that setting the attribute to any non-zero integer results in setting the attribute to 1
        self::assertEquals(1, $this->frmtr->getAttribute(NumberFormatter::LENIENT_PARSE));

        // the ICU documentation says that lenient parse will parse all grouping separators no matter where they are
        $pos = 0;
        $expectedResult = 123456;
        self::assertEquals($expectedResult, $this->frmtr->parse('1234,56', NumberFormatter::TYPE_INT64, $pos));
        self::assertEquals(7, $pos);

        $pos = 0;
        $expectedResult = 123456;
        self::assertEquals($expectedResult, $this->frmtr->parse('123,4,56', NumberFormatter::TYPE_INT64, $pos));
        self::assertEquals(8, $pos);

        // even two in a row
        $pos = 0;
        $expectedResult = 123456;
        self::assertEquals($expectedResult, $this->frmtr->parse('1234,,56', NumberFormatter::TYPE_INT64, $pos));
        self::assertEquals(8, $pos);

        // what is the relationship between the pattern and lenient parse?
        $pattern = '#0';
        $this->frmtr->setPattern($pattern);
        self::assertEquals($pattern, $this->frmtr->getPattern());

        // the pattern overrides the attribute:  if there is no grouping separator in the pattern then the parser
        // will not parse the separator, lenient mode or not.
        $pos = 0;
        $expectedResult = 1234;
        self::assertEquals($expectedResult, $this->frmtr->parse('1234,56', NumberFormatter::TYPE_INT64, $pos));
        self::assertEquals(4, $pos);

    }

}
