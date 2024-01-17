<?php

declare (strict_types=1);
/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */

namespace tests\intl\messageformatter;

use MessageFormatter;
use PHPUnit\Framework\TestCase;

class MessageFormatterTest extends TestCase
{
    public function testMessageFormatterConstructionWithArbitraryStrings() : void
    {
        $patternWithNoPlaceholders = "This is some silly string";
        // can create with a bad locale
        $frmtr = MessageFormatter::create("zz", $patternWithNoPlaceholders);
        // echoes back the pattern
        self::assertEquals($patternWithNoPlaceholders, $frmtr->format([]));
    }

    public function testBasicStringSubstitution() : void
    {
        $patternWithOnePlaceholder = "{name}";
        $frmtr = MessageFormatter::create("zz", $patternWithOnePlaceholder);
        $expectedResult = "John";
        self::assertEquals($expectedResult, $frmtr->format(["name" => "John"]));
    }

    public function testTwoParameters() : void
    {
        $patternWithTwoPlaceholders = "{firstname} {lastname}";
        $frmtr = MessageFormatter::create("zz", $patternWithTwoPlaceholders);

        // basic test
        $expectedResult = "John Smith";
        self::assertEquals($expectedResult, $frmtr->format(["firstname" => "John", "lastname" => "Smith"]));

        // named parameters can be in any order
        $expectedResult = "John Smith";
        self::assertEquals($expectedResult, $frmtr->format(["lastname" => "Smith", "firstname" => "John"]));

        // using numeric keys just returns the pattern
        self::assertEquals($patternWithTwoPlaceholders, $frmtr->format(["John", "Smith"]));
    }

    public function getNumberFormattingLocalesAndStyles() : array
    {
        return [
            /** NUMBERS */
            // rounds down (truncating)
            ["en", "integer", 10.5, "10"],
            // thousands separator insertion
            ["en", "integer", 1000000, "1,000,000"],
            // thousands separator insertion for a different locale
            ["de", "integer", 1000000, "1.000.000"],
            // percent formatting
            ["en", "percent", 0.50, "50%"],
            // currency formatting in USD
            ["en", "::currency/USD", 123.45, "$123.45"],
            // currency formatting in Germany using a more complete skeleton.  "\u{00A0} is non breaking space
            // and "\u{20AC" is the symbol for euros, compact short truncates the fractional euros
            ["de", "::sign-always compact-short currency/EUR", 123.45, "+123" . "\u{00A0}\u{20AC}"],
        ];
    }

    /**
     * testNumberFormatting
     * @param string $locale
     * @param string $style
     * @param string $expectedResult
     * @dataProvider getNumberFormattingLocalesAndStyles
     * a complete documentation for number format skeletons ("style" parameter below) can be found here:
     * https://unicode-org.github.io/icu/userguide/format_parse/numbers/skeletons.html
     */
    public function testNumberFormatting(
        string $locale,
        string $style,
        mixed $variableValue,
        string $expectedResult
    ): void {
        $placeholder = "{0, number, " . $style . "}";
        $frmtr = MessageFormatter::create($locale, $placeholder);
        self::assertEquals($expectedResult, $frmtr->format([$variableValue]));
    }

    public function getDateFormattingLocalesAndStyles() : array
    {
        return [
            // short date, american style
            ["en", "short", strtotime("2013-05-23"), "5/23/13"],
            // short date, german style
            ["de", "short", strtotime("2013-05-23"), "23.05.13"],
            // medium date, american style
            ["en", "medium", strtotime("2013-05-23"), "May 23, 2013"],
            // long date, american style (different than medium how?)
            ["en", "long", strtotime("2013-05-23"), "May 23, 2013"],

        ];
    }

    /**
     * testDateFormatting
     * @param string $locale
     * @param string $style
     * @param mixed $variableValue
     * @param string $expectedResult
     * @dataProvider getDateFormattingLocalesAndStyles
     */
    public function testDateFormatting(
        string $locale,
        string $style,
        mixed $variableValue,
        string $expectedResult
    ): void {
        $placeholder = "{varname, date, " . $style . "}";
        $frmtr = MessageFormatter::create($locale, $placeholder);
        self::assertEquals($expectedResult, $frmtr->format(["varname" => $variableValue]));
    }

    public function getTimeFormattingLocalesAndStyles() : array
    {
        return [
            // short time, american style
            ["en", "short", strtotime("2022-01-01 12:34:45"), "12:34 PM"],
            // short time, german style
            ["de", "short", strtotime("2022-01-01 12:34:45"), "12:34"],
            // medium time, american style
            ["en", "medium", strtotime("2022-01-01 12:34:45"), "12:34:45 PM"],
            // long time, american style (timezone pulled from my computer)
            ["en", "long", strtotime("2022-01-01 12:34:45"), "12:34:45 PM EST"],
        ];
    }

    /**
     * testTimeFormatting
     * @param string $locale
     * @param string $style
     * @param mixed $variableValue
     * @param string $expectedResult
     * @dataProvider getTimeFormattingLocalesAndStyles
     */
    public function testTimeFormatting(
        string $locale,
        string $style,
        mixed $variableValue,
        string $expectedResult
    ): void {
        $placeholder = "{varname, time, " . $style . "}";
        $frmtr = MessageFormatter::create($locale, $placeholder);
        self::assertEquals($expectedResult, $frmtr->format(["varname" => $variableValue]));
    }


    public function getSelectNamedParams() : array
    {
        return [
            ["male", "He"],
            ["female", "She"],
        ];
    }

    /**
     * testSelection
     * @param string $friendName
     * @param string $gender
     * @param string $expectedResult
     * @dataProvider getSelectNamedParams
     */
    public function testSelectionNamedParameter(string $gender, string $expectedResult) : void
    {
        $locale = "en_US";
        $msg = '{gender, select, female {She} male {He} other {They}}';
        $frmtr = MessageFormatter::create($locale, $msg);
        $parameters = ['gender' => $gender];
        self::assertEquals($expectedResult, $frmtr->format($parameters));
    }

    public function getSelectPositionalParams() : array
    {
        return [
            ["male", "He"],
            ["female", "She"],
        ];
    }

    /**
     * testSelectionPositionalParameter
     * @param string $gender
     * @param string $expectedResult
     * @dataProvider getSelectPositionalParams
     */
    public function testSelectionPositionalParameter(string $gender, string $expectedResult) : void
    {
        $locale = "en_US";
        $msg = '{0, select, female {She} male {He} other {They}}';
        $frmtr = MessageFormatter::create($locale, $msg);
        $parameters = [$gender];
        self::assertEquals($expectedResult, $frmtr->format($parameters));
    }

    public function getPluralizationParams() : array
    {
        return [
            [0, "no apples"],
            [1, "one apple"],
            [5, "5 apples"],
        ];
    }

    /**
     * testPluralization
     * @param int $count
     * @param string $expectedResult
     * @dataProvider getPluralizationParams
     */
    public function testPluralization(int $count, string $expectedResult) : void
    {
        $locale = "en_US";
        $msg = '{appleCount, plural, =0 {no apples} one {one apple} other {# apples}}';
        $frmtr = MessageFormatter::create($locale, $msg);
        $parameters = ["appleCount" => $count];
        self::assertEquals($expectedResult, $frmtr->format($parameters));
    }

    public function getOrdinalParams() : array
    {
        return [
            [1, "1st"],
            [2, "2nd"],
            [3, "3rd"],
            [5, "5th"],
        ];
    }

    /**
     * testOrdinal
     * @param int $count
     * @param string $expectedResult
     * @dataProvider getOrdinalParams
     */
    public function testOrdinal(int $count, string $expectedResult) : void
    {
        $locale = "en_US";
        // this is a little cheesy but ok, we get the point.  What is the answer to create
        // first, second, third etc ?
        $msg = '{place, selectordinal, one {#st} two {#nd} few {#rd} other {#th}}';
        $frmtr = MessageFormatter::create($locale, $msg);
        $parameters = ["place" => $count];
        self::assertEquals($expectedResult, $frmtr->format($parameters));
    }

    public function getOrdinalParamsBeCareful() : array
    {
        return [
            [1, "1th"],
            [2, "2th"],
            [3, "3th"],
            [5, "5th"],
        ];
    }

    /**
     * testOrdinalBeCareful
     * @param int $count
     * @param string $expectedResult
     * @dataProvider getOrdinalParamsBeCareful
     */
    public function testOrdinalBeCareful(int $count, string $expectedResult) : void
    {
        $locale = "en_US";
        // be careful, apparently numerics as indices for the choices do not work!
        $msg = '{place, selectordinal, 1 {#st} 2 {#nd} 3 {#rd} other {#th}}';
        $frmtr = MessageFormatter::create($locale, $msg);
        $parameters = ["place" => $count];
        self::assertEquals($expectedResult, $frmtr->format($parameters));
    }
}
