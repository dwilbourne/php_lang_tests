<?php
/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version 1.0
 */

use PHPUnit\Framework\TestCase;

class ParseStrTest extends TestCase
{
    public function testParseStrBasic()
    {
        // right out of the manual, except uses a named index
        // in one of the array values
        $queryString = 'first=value&arr[]=foo+bar&arr[quux]=baz';
        $expectedResult = [
            'first' => 'value',
            'arr' => ['foo bar', 'quux' => 'baz']
        ];
        parse_str(urldecode($queryString), $actualResult);
        self::assertEquals($expectedResult, $actualResult);
    }

    public function testParseStrEmptyString()
    {
        $queryString = '';
        $expectedResult = [];
        parse_str(urldecode($queryString), $actualResult);
        self::assertEquals($expectedResult, $actualResult);
    }

    public function testParseStrBadArraySyntax()
    {
        $queryString = 'first[=value[land]';
        // key gets mangled to conform to php variable naming rules
        $expectedResult = ['first_' => 'value[land]'];
        parse_str(urldecode($queryString), $actualResult);
        self::assertEquals($expectedResult, $actualResult);
    }

    public function testParseStrMultipleEqualsSigns()
    {
        $queryString = 'first=value=second';
        // everything after the first equals sign is considered the variable value
        $expectedResult = ['first' => 'value=second'];
        parse_str(urldecode($queryString), $actualResult);
        self::assertEquals($expectedResult, $actualResult);
    }

    public function testParseStrNoEqualsSign()
    {
        $queryString = 'first&second';
        // assigns an empty string as the value if there is no explicit assignment
        $expectedResult = ['first' => '', ' second' => ''];
        parse_str(urldecode($queryString), $actualResult);
        self::assertEquals($expectedResult, $actualResult);
    }

    /*
     * This example illustrates a potential problem with parse_str.  'Name mangling' means that the keys in
     * the array must conform to PHP variable naming rules, so dots and spaces are converted to underscores.
     * Once parsed, reconstituting the original url is impossible.
     */
    public function testParseStrNameMangling()
    {
        $queryString = 'first%20key=some%20value';
        $expectedResult = ['first_key' => 'some value'];
        parse_str(urldecode($queryString), $actualResult);
        self::assertEquals($expectedResult, $actualResult);
    }
}
