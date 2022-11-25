<?php
/**
 * @package: pvc
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version: 1.0
 */

namespace tests\intl\numberformatter;

use PHPUnit\Framework\TestCase;
use pvc\unicode\Unicode2Utf8;

class NumberFormatterSymbolTest extends TestCase {

    function testSetSymbolWithoutQuotesOnDecimalFormatter() {
        // if symbol is not surrounded by quotes, the set fails
        $frmtr = new \NumberFormatter('en-US', \NumberFormatter::DECIMAL);
        $newPattern = "#,##0.###@";
        self::assertFalse($frmtr->setPattern($newPattern));
    }

    function testSetSymbolWithQuotesOnDecimalFormatter() {
        // in order for the set to work, the symbol must be enclosed in quotes
        $frmtr = new \NumberFormatter('en-US', \NumberFormatter::DECIMAL);
        $newPattern = "#,##0.###'@'";
        $frmtr->setPattern($newPattern);

        // quotes do not get included in the pattern..
        $expectedResult = '#,##0.###@';
        self::assertEquals($expectedResult, $frmtr->getPattern());

    }

    function testSetSymbolOnCurrencyFormatter() {
        $frmtr = new \NumberFormatter('en-US', \NumberFormatter::CURRENCY);
        $newPattern = "#,##0.###'@'";
        $frmtr->setPattern($newPattern);

        // the formatter type simply provides a default pattern.
        $expectedResult = '#,##0.###@';
        self::assertEquals($expectedResult, $frmtr->getPattern());
    }

    function testChangeSymbols() {

        $frmtr = new \NumberFormatter('de-DE', \NumberFormatter::CURRENCY);
        $actualPattern = $frmtr->getPattern();

        $spacer = mb_substr($actualPattern, 8, 1);
        // the space is actually equal to unicode value 160 even though the pattern looks like '#,##0.00 ¤'
        self::assertEquals(160, Unicode2Utf8::uniord($spacer));

        $expectedPattern = '#,##0.00' . Unicode2Utf8::unichr(160) . '¤';
        self::assertEquals($expectedPattern, $actualPattern);

        $germanMoneySymbol = $frmtr->getSymbol(\NumberFormatter::CURRENCY_SYMBOL);
        self::assertEquals('€', $germanMoneySymbol);

        $formattedValue = '1.234,56' . Unicode2Utf8::unichr(160) . $germanMoneySymbol;
        $numericValue = 1234.56;

        // here is the format method
        self::assertEquals($formattedValue, $frmtr->format($numericValue, \NumberFormatter::TYPE_DOUBLE));

        // and here is the parse method
        self::assertEquals($numericValue, $frmtr->parse($formattedValue, \NumberFormatter::TYPE_DOUBLE));

        self::assertTrue($frmtr->setSymbol(\NumberFormatter::MONETARY_SEPARATOR_SYMBOL, '.'));
        self::assertTrue($frmtr->setSymbol(\NumberFormatter::MONETARY_GROUPING_SEPARATOR_SYMBOL, ','));

        // pattern should not change
        self::assertEquals($expectedPattern, $frmtr->getPattern());

        // but parsing should now work differently - the decimal point is changed to a comma and vice versa
        $testString = '1,234.56' . Unicode2Utf8::unichr(160) . $germanMoneySymbol;
        self::assertEquals($numericValue, $frmtr->parse($testString, \NumberFormatter::TYPE_DOUBLE));


    }

    function testSpacerInFrench() {
        $frmtr = new \NumberFormatter('fr-FR', \NumberFormatter::CURRENCY);
        $actualPattern = $frmtr->getPattern();
        echo $actualPattern . PHP_EOL;

        $spacer = mb_substr($actualPattern, 8, 1);
        // the space is actually equal to unicode value 160 even though the pattern looks like '#,##0.00 ¤'
        // This is the same as in the German above
        self::assertEquals(160, Unicode2Utf8::uniord($spacer));

        $expectedPattern = '#,##0.00' . Unicode2Utf8::unichr(160) . '¤';
        self::assertEquals($expectedPattern, $actualPattern);

    }

    function testLiteralPositionsInPositiveSubpattern() {
        $frmtr = new \NumberFormatter('en-US', \NumberFormatter::CURRENCY);
        $newPattern = '{' . $frmtr->getPattern();
        self::assertTrue($frmtr->setPattern($newPattern));
        self::assertEquals($newPattern, $frmtr->getPattern());

        // the 'prefix' is any combination of symbols that appears left of the digits.  Literals may appear anywhere
        // in the prefix.
        $newPattern = '¤{#,##0.00';
        self::assertTrue($frmtr->setPattern($newPattern));
        self::assertEquals($newPattern, $frmtr->getPattern());

        $newPattern = '{¤}#,##0.00';
        self::assertTrue($frmtr->setPattern($newPattern));
        self::assertEquals($newPattern, $frmtr->getPattern());

        // literals may not appear within the digit sequence or inside the fractional digit sequence
        $newPattern = "¤#{,##0.00";
        self::assertFalse($frmtr->setPattern($newPattern));

        // literals may not appear within the digit sequence or inside the fractional digit sequence
        $newPattern = "¤#,##0.0}0";
        self::assertFalse($frmtr->setPattern($newPattern));

        // grouping separator may not appear within the fractional digit sequence
        $newPattern = "¤#,##0.00,00";
        self::assertFalse($frmtr->setPattern($newPattern));

        // the 'suffix' is any combination of symbols that appears right of the (fractional) digits.  Literals may appear anywhere
        // in the suffix.
        $newPattern = '¤#,##0.00}';
        self::assertTrue($frmtr->setPattern($newPattern));
        self::assertEquals($newPattern, $frmtr->getPattern());

        $newPattern = '#,##0.00{¤}';
        self::assertTrue($frmtr->setPattern($newPattern));
        self::assertEquals($newPattern, $frmtr->getPattern());

    }

    // the question this test tries to answer is whether the presence of any kind of symbol in the negative
    // subpattern effectively acts like a minus sign to the extent it is not mirrored in the same position
    // in the positive subpattern.

    function testLiteralsInNegativeSubpattern() {

        $frmtr = new \NumberFormatter('en-US', \NumberFormatter::DECIMAL);
        $newPattern = '#,##0.###;{#,##0.###';
        self::assertTrue($frmtr->setPattern($newPattern));
        self::assertEquals($newPattern, $frmtr->getPattern());
        $expectedValue = -123.45;
        $input = '{123.45';
        self::assertEquals($expectedValue, $frmtr->parse($input));

        $newPattern = '(#,##0.###)};{(#,##0.###)}';
        self::assertTrue($frmtr->setPattern($newPattern));
        self::assertEquals($newPattern, $frmtr->getPattern());

        $input = '123.45)}';
        self::assertFalse($frmtr->parse($input));

        $expectedValue = 123.45;
        $input = '(123.45)}';
        self::assertEquals($expectedValue, $frmtr->parse($input));

        $expectedValue = -123.45;
        $input = '{(123.45)}';
        self::assertEquals($expectedValue, $frmtr->parse($input));

        // how about shorthand?  No - you cannot just put in 'the difference between
        // the positive and negative subpatterns' into the negative subpattern.

        $newPattern = '(#,##0.###)};{#';
        self::assertTrue($frmtr->setPattern($newPattern));
        // not what we were looking for
        $actualPattern = '(#,##0.###)};{#,##0.###';
        self::assertEquals($actualPattern, $frmtr->getPattern());

    }

    function testReservedSymbolsInOddPlaces() {

        $frmtr = new \NumberFormatter('en-US', \NumberFormatter::DECIMAL);
        $newPattern = '-#,##0.###;#,##0.###';
        self::assertTrue($frmtr->setPattern($newPattern));
        self::assertEquals($newPattern, $frmtr->getPattern());

        $expectedValue = 123.45;
        $input = '-123.45';
        self::assertEquals($expectedValue, $frmtr->parse($input));

        $expectedValue = -123.45;
        $input = '123.45';
        self::assertEquals($expectedValue, $frmtr->parse($input));
    }

    function testLiteralInOddPlaces() {

        $frmtr = new \NumberFormatter('en-US', \NumberFormatter::DECIMAL);
        $newPattern = '#,##0*###';
        // cannot put literals into the middle of the digit sequence
        self::assertFalse($frmtr->setPattern($newPattern));

    }

    function testPositionalSymbolInOddPlaces() {

        $frmtr = new \NumberFormatter('en-US', \NumberFormatter::DECIMAL);
        $newPattern = '#,##0¤###';
        // cannot put currency into the middle of the digit sequence
        self::assertFalse($frmtr->setPattern($newPattern));

    }

    function testCurrencySymbolInDecimalPattern() {

        $frmtr = new \NumberFormatter('en-US', \NumberFormatter::DECIMAL);
        // does it matter if we use a currency symbol in a decimal pattern?
        $newPattern = '¤-#,##0.###;#,##0.###¤';
        self::assertTrue($frmtr->setPattern($newPattern));
        self::assertEquals($newPattern, $frmtr->getPattern());

        // yes - it matters that it is a DECIMAL formatter - currency symbol is still substituted properly
        // but the parser fails ungracefully

        // this fails badly
        //$expectedValue = 123.45;
        //$input = '$-123.45';
        //self::assertEquals($expectedValue, $frmtr->parse($input));

        // this fails badly
        //$expectedValue = -123.45;
        //$input = '123.45$';
        //self::assertEquals($expectedValue, $frmtr->parse($input));

        // but as a formatter of type currency, it works fine
        $frmtr = new \NumberFormatter('en-US', \NumberFormatter::CURRENCY);
        $newPattern = '¤-#,##0.###;#,##0.###¤';
        self::assertTrue($frmtr->setPattern($newPattern));
        self::assertEquals($newPattern, $frmtr->getPattern());

        $expectedValue = 123.45;
        $input = '$-123.45';
        self::assertEquals($expectedValue, $frmtr->parse($input));

        $expectedValue = -123.45;
        $input = '123.45$';
        self::assertEquals($expectedValue, $frmtr->parse($input));
    }

    function testPercentSymbolInDecimalPattern() {
        // is the same true for percent symbol in decimal pattern?
        $frmtr = new \NumberFormatter('en-US', \NumberFormatter::DECIMAL);
        // does it matter if we use a percent symbol in a decimal pattern?
        $newPattern = '%-#,##0.###;#,##0.###%';
        self::assertTrue($frmtr->setPattern($newPattern));
        self::assertEquals($newPattern, $frmtr->getPattern());

        // interesting:  looks like you can put the percent symbol in either the prefix or
        // suffix and it 'functions'
        // but the parser fails ungracefully

        $expectedValue = 1.2345;
        $input = '%-123.45';
        self::assertEquals($expectedValue, $frmtr->parse($input));

        // Percent can only appear once in the pattern - the following set fails
        $newPattern = '%#,##0.###%;-%#,##0.###%';
        self::assertFalse($frmtr->setPattern($newPattern));
    }

    function testPercentInCurrencyFormatter() {
        // what if we try a percent symbol in a currency formatter?
        $newPattern = '%#,##0.###';
        $frmtr = new \NumberFormatter('en-US', \NumberFormatter::CURRENCY);
        self::assertTrue($frmtr->setPattern($newPattern));
        self::assertEquals($newPattern, $frmtr->getPattern());

        // sure - percent is independent of the formatter type.
        $expectedValue = 1.2345;
        $input = '%123.45';
        self::assertEquals($expectedValue, $frmtr->parse($input));
    }

    function testCurrencyInPercentFormatter() {

        $newPattern = '¤#,##0.###';
        $frmtr = new \NumberFormatter('en-US', \NumberFormatter::PERCENT);
        self::assertTrue($frmtr->setPattern($newPattern));
        self::assertEquals($newPattern, $frmtr->getPattern());

        // fails badly, like using a currency symbol in a decimal formatter
        //$expectedValue = 123.45;
        //$input = '$123.45';
        //self::assertEquals($expectedValue, $frmtr->parse($input));

    }

    function testDecimalSymbolInPrefix() {
        // can we put a decimal separator into the prefix?
        $frmtr = new \NumberFormatter('en-US', \NumberFormatter::DECIMAL);
        // it needs to be quoted, a behavior that is usually built into the set prefix text attribute
        $frmtr->setTextAttribute(\NumberFormatter::POSITIVE_PREFIX, '.');
        $frmtr->setTextAttribute(\NumberFormatter::NEGATIVE_PREFIX, '.-');
        // but in this case it does not work as expected.  The minus sign is quoted but not the decimal point.
        $expectedPattern = ".#,##0.###;.'-'#,##0.###¤";
        self::assertEquals($expectedPattern, $frmtr->getPattern());

    }



}
