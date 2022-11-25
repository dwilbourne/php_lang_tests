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
 * Class NumberFormatterNegationAffirmationParseTest
 */
class NumberFormatterPlusMinusTest extends TestCase {

    protected $frmtr;

    function setUp() : void {
        $this->frmtr = new \NumberFormatter('en-US', NumberFormatter::DECIMAL );
    }

    function testParsePlusBasic() {
        $pattern = "#,##0.###";
        self::assertEquals($pattern, $this->frmtr->getPattern());

        $expectedResult = 12345;
        $plusSign = $this->frmtr->getSymbol(NumberFormatter::PLUS_SIGN_SYMBOL);

        // set to leading plus sign.  Notice that it automatically adds the pattern for
        // standard negation at this point and it has put quotes around the plus sign.
        self::assertTrue($this->frmtr->setTextAttribute(NumberFormatter::POSITIVE_PREFIX, $plusSign));
        $pattern = "'+'#,##0.###;-#,##0.###";
        self::assertEquals($pattern, $this->frmtr->getPattern());

        // test parsing
        $this->assertEquals($expectedResult, $this->frmtr->parse("+12345", NumberFormatter::TYPE_INT64));
        // note that it MUST have a leading plus sign
        $this->assertFalse($this->frmtr->parse("12345", NumberFormatter::TYPE_INT64));

        // if we remove the quotes surrounding the plus sign, does the behavior change?
        $pattern = "+#,##0.###;-#,##0.###";
        self::assertTrue($this->frmtr->setPattern($pattern));
        self::assertEquals($pattern, $this->frmtr->getPattern());

        // nope - the plus sign is still required for parsing
        $this->assertFalse($this->frmtr->parse("12345", NumberFormatter::TYPE_INT64));

        // what if we change the plus sign symbol now?
        $newSymbol = '*';
        self::assertTrue($this->frmtr->setSymbol(\NumberFormatter::PLUS_SIGN_SYMBOL, $newSymbol));
        self::assertEquals($newSymbol, $this->frmtr->getSymbol(\NumberFormatter::PLUS_SIGN_SYMBOL));

        $expectedResult = 12345;
        self::assertEquals($expectedResult, $this->frmtr->parse('*12345'));

    }

    function testParseChangePlusSigninPatternManually() {

        // what if we change the symbol for plus sign?
        $this->frmtr->setTextAttribute(\NumberFormatter::POSITIVE_PREFIX, '^');
        $pattern = "^#,##0.###;-#,##0.###";
        self::assertEquals($pattern, $this->frmtr->getPattern());

        $this->assertFalse($this->frmtr->parse("@12345", NumberFormatter::TYPE_INT64));

        $this->assertEquals(12345, $this->frmtr->parse("^12345", NumberFormatter::TYPE_INT64));
        $this->assertFalse($this->frmtr->parse("12345", NumberFormatter::TYPE_INT64));

        // now what if we add quotes around our new plus sign - does that matter?
        $quotedPattern = "'^'#,##0.###;-#,##0.###";
        self::assertTrue($this->frmtr->setPattern($quotedPattern));
        // interestingly, setPattern removes the quotes.
        $unquotedPattern = "^#,##0.###;-#,##0.###";
        self::assertEquals($unquotedPattern, $this->frmtr->getPattern());

        // what about adding quoted plus sign manually via setPattern
        $quotedPattern = "'+'#,##0.###;-#,##0.###";
        self::assertTrue($this->frmtr->setPattern($quotedPattern));
        // interestingly, setPattern removes the quotes from all symbols except the plus sign.  The plus sign is
        // interpreted literally when quoted or it is replaced by whatever is set as the plus sign symbol if unquoted.
        $unquotedPattern = "+#,##0.###;-#,##0.###";
        self::assertEquals($unquotedPattern, $this->frmtr->getPattern());
    }

    function testParseOnManuallySetDefaultPattern() {
        // this is an explicit version of the default pattern
        $pattern = "#,##0.###;-#,##0.###";
        self::assertTrue($this->frmtr->setPattern($pattern));
        // it automatically removes the default negative subpattern
        $expectedPattern = "#,##0.###";
        self::assertEquals($expectedPattern, $this->frmtr->getPattern());
        // now what about parsing
        self::assertEquals(-123.1, $this->frmtr->parse('-123.1'));

    }

    function testChangingPrefixedMinusSign() {

        $symbol = '*';
        self::assertTrue($this->frmtr->setSymbol(\NumberFormatter::MINUS_SIGN_SYMBOL, $symbol));
        $expectedPattern = '#,##0.###';
        self::assertEquals($expectedPattern, $this->frmtr->getPattern());

        // now what if we trying parsing a negative number
        $expectedValue = -12345;
        self::assertEquals($expectedValue, $this->frmtr->parse('*12345'));

        // now let's try to make the negative subpattern explicit and see about changing symbols.
        $newPattern = '#,##0.###;-#,##0.###';
        self::assertTrue($this->frmtr->setPattern($newPattern));
        // even though the set "succeeds", the pattern reverts to the old pattern.
        self::assertEquals('#,##0.###', $this->frmtr->getPattern());

        // does the pattern change if we set the negatived prefix to null when 'it does not exist'?
        // Yes! Then the negative subpattern becomes explicit.
        $this->frmtr->setTextAttribute(\NumberFormatter::NEGATIVE_PREFIX, '');
        self::assertEquals('#,##0.###;#,##0.###', $this->frmtr->getPattern());

    }

    function testLiteralSuffixedMinusSign() {

        // note that you have to remove the leading minus sign from the negative subpattern
        $this->frmtr->setTextAttribute(\NumberFormatter::NEGATIVE_PREFIX, '');
        $this->frmtr->setTextAttribute(\NumberFormatter::NEGATIVE_SUFFIX, $this->frmtr->getSymbol(\NumberFormatter::MINUS_SIGN_SYMBOL));
        // like the plus sign symbol, this ends up being quoted
        $expectedPattern = "#,##0.###;#,##0.###'-'";
        self::assertEquals($expectedPattern, $this->frmtr->getPattern());

        $expectedValue = -12345;
        self::assertEquals($expectedValue, $this->frmtr->parse('12345-'));

        // if we change the minus sign symbol it should make no difference - the minus sign in the quotes is
        // treated as a literal
        self::assertTrue($this->frmtr->setSymbol(\NumberFormatter::MINUS_SIGN_SYMBOL, '&'));
        self::assertFalse($this->frmtr->parse('12345&'));
    }

    function testListeralSuffixedMinusSignAndCurrencySymbol() {
        $this->frmtr->setTextAttribute(\NumberFormatter::NEGATIVE_PREFIX, '');
        $suffix = $this->frmtr->getSymbol(\NumberFormatter::MINUS_SIGN_SYMBOL) . '¤';
        self::assertEquals($suffix, '-¤');
        $this->frmtr->setTextAttribute(\NumberFormatter::NEGATIVE_SUFFIX, $suffix);
        // like the plus sign symbol, this ends up being quoted
        $expectedPattern = "#,##0.###;#,##0.###'-''¤'";
        self::assertEquals($expectedPattern, $this->frmtr->getPattern());
    }

    function testListeralSuffixedMinusSignAndCurrencySymbolUnquoted() {
        $this->frmtr->setTextAttribute(\NumberFormatter::NEGATIVE_PREFIX, '');
        $newPattern = $this->frmtr->getPattern() . $this->frmtr->getSymbol(\NumberFormatter::MINUS_SIGN_SYMBOL) . '¤';
        $expectedPattern = "#,##0.###;#,##0.###-¤";
        self::assertEquals($expectedPattern, $newPattern);
        self::assertTrue($this->frmtr->setPattern($newPattern));
        self::assertEquals($expectedPattern, $this->frmtr->getPattern());
    }

    function testUnquotedSuffixedMinusSign() {
        // now let's remove the quotes
        $patternWithoutQuotes = "#,##0.###;#,##0.###-";
        self::assertTrue($this->frmtr->setPattern($patternWithoutQuotes));
        self::assertEquals($patternWithoutQuotes, $this->frmtr->getPattern());

        // behaves as before
        $expectedValue = -12345;
        self::assertEquals($expectedValue, $this->frmtr->parse('12345-'));

        // now let's change the minus sign symbol
        $symbol = '*';
        self::assertTrue($this->frmtr->setSymbol(\NumberFormatter::MINUS_SIGN_SYMBOL, $symbol));
        // pattern does not change
        self::assertEquals($patternWithoutQuotes, $this->frmtr->getPattern());

        // now try parsing a different string
        $expectedValue = -12345;
        self::assertEquals($expectedValue, $this->frmtr->parse('12345*'));

        // perfect:  unquoted + and - signs are used as wildcards and when they are quoted they are literals.

    }

    function testParseWithTrailingPlusSign() {

        // set to parse trailing plus sign
        $this->frmtr->setTextAttribute(NumberFormatter::POSITIVE_PREFIX, '');
        $this->frmtr->setTextAttribute(NumberFormatter::POSITIVE_SUFFIX, '+');

        // test parsing
        $expectedResult = 12345;
        $this->assertEquals($expectedResult, $this->frmtr->parse("12345+", NumberFormatter::TYPE_INT64));
        // note that it MUST have a trailing plus sign
        $this->assertFalse($this->frmtr->parse("12345", NumberFormatter::TYPE_INT64));

    }

    function testAffirmationWithDifferentSymbol() {
        $this->frmtr->setSymbol(\NumberFormatter::PLUS_SIGN_SYMBOL, '&');
        // is it possible to specify a different symbol?
        $newPattern = "+#,##0.###";
        $this->frmtr->setPattern($newPattern);
        self::assertEquals($newPattern, $this->frmtr->getPattern());
        $expectedResult = 12345;
        $this->assertEquals($expectedResult, $this->frmtr->parse("&12345", NumberFormatter::TYPE_INT64));
    }

    function testRespecifyStockSymbol() {
        // this is a bug in the NumberFormatter - it should not be quoted.
        $expectedPattern = "#,##0.###;'-'#,##0.###";
        $symbol = $this->frmtr->getSymbol(\NumberFormatter::MINUS_SIGN_SYMBOL);
        $this->frmtr->setTextAttribute(\NumberFormatter::NEGATIVE_PREFIX, $symbol);
        $this->assertEquals($expectedPattern, $this->frmtr->getPattern());

        // here's the bug, this should parse correctly
        $this->frmtr->setSymbol(\NumberFormatter::MINUS_SIGN_SYMBOL, '&');
        $expectedResult = -12345;
        $this->assertNotEquals($expectedResult, $this->frmtr->parse('&12345'));
    }

    function testNegation() {

        // standard negation
        $expectedResult = -12345;
        $this->assertEquals($expectedResult, $this->frmtr->parse("-12345", NumberFormatter::TYPE_INT64));

        // negative suffix
        // there's no doubt the numberformatter is a little quirky.  If you set a negative suffix, the
        // numberformatter by default will include a negative prefix as well in the pattern.
        $basicPattern = '#,##0.###';
        self::assertEquals($basicPattern, $this->frmtr->getPattern());
        $this->frmtr->setTextAttribute(NumberFormatter::NEGATIVE_SUFFIX, '-');
        $newStrangePattern = "#,##0.###;-#,##0.###'-'";
        self::assertEquals($newStrangePattern, $this->frmtr->getPattern());
        $correctPattern = "#,##0.###;#,##0.###'-'";
        $this->frmtr->setTextAttribute(NumberFormatter::NEGATIVE_PREFIX, '');
        self::assertEquals($correctPattern, $this->frmtr->getPattern());

        $expectedResult = -12345;
        $this->assertEquals($expectedResult, $this->frmtr->parse("12345-", NumberFormatter::TYPE_INT64));

        // if you really want to require both a leading and trailing minus symbol, then here you go:
        $this->frmtr->setTextAttribute(NumberFormatter::NEGATIVE_PREFIX, '-');
        $expectedResult = -12345;
        $this->assertEquals($expectedResult, $this->frmtr->parse("-12345-", NumberFormatter::TYPE_INT64));


        // make this a little different and see how it handles simultaneous prefix and suffix
        $this->frmtr->setTextAttribute(NumberFormatter::NEGATIVE_PREFIX, '(');
        $this->frmtr->setTextAttribute(NumberFormatter::NEGATIVE_SUFFIX, ')');
        $expectedResult = -12345;

        $this->assertEquals($expectedResult, $this->frmtr->parse("(12345)", NumberFormatter::TYPE_INT64));

        // fails - both parentheses are required - leading parenthesis becomes 'leading debris'
        $this->assertFalse($this->frmtr->parse("(12345", NumberFormatter::TYPE_INT64));

        $expectedResult = 12345;
        // succeeds - trailing parenthesis is treated as 'debris' which kills the parser and has the
        // somewhat unexpected result of parsing the first portion of the string as a positive number
        $this->assertEquals($expectedResult, $this->frmtr->parse("12345)", NumberFormatter::TYPE_INT64));

    }

    function testCombiningSubpatterns() {

        // you can specify a negation pattern right after the affirmation pattern using the pattern separator (';');
        // if no negation pattern is specified, then negation is assumed to be a minus sign symbol prefixed to the
        // affirmation (positive) pattern

        // note that the pattern below is localized.. '+' has no special significance in 'pattern language',
        // nor does '(' or ')', so this pattern makes sense, maybe, in certain locales.

        $pattern = '#0+;(#0)';
        $this->frmtr->setPattern($pattern);

        $expectedResult = 12345;
        $this->assertEquals($expectedResult, $this->frmtr->parse("12345+", NumberFormatter::TYPE_INT64));

        $expectedResult = -12345;
        $this->assertEquals($expectedResult, $this->frmtr->parse("(12345)", NumberFormatter::TYPE_INT64));


    }

    function testChangingPatternManually() {

        $pattern = '+#,##0.###;#,##0.###-';
        $this->frmtr->setPattern($pattern);

        $expectedResult = 12345;
        $this->assertEquals($expectedResult, $this->frmtr->parse("+12345", NumberFormatter::TYPE_INT64));

        $expectedResult = -12345;
        $this->assertEquals($expectedResult, $this->frmtr->parse("12345-", NumberFormatter::TYPE_INT64));


    }


}