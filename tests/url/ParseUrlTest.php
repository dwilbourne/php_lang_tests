<?php
/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version 1.0
 */

use PHPUnit\Framework\TestCase;

class ParseUrlTest extends TestCase
{
    public function testParseUrlStringBasic()
    {
        $urlString = 'http://username:password@hostname:9090/some/path?arg=value#anchor';
        $values = array(
            'scheme' => 'http',
            'host' => 'hostname',
            'port' => '9090',
            'user' => 'username',
            'pass' => 'password',
            'path' => '/some/path',
            'query' => 'arg=value',
            'fragment' => 'anchor'
        );
        $result = parse_url($urlString);
        foreach($values as $key => $value) {
            self::assertTrue($values[$key] == $result[$key]);
        }
        self::assertSameSize($values, $result);
    }

    public function testMissingUrlParts()
    {
        $url = '//www.example.com/path?googleguy=googley';

        $expectedHost = 'www.example.com';
        $expectedPath = '/path';
        $expectedQuery = 'googleguy=googley';

        self::assertNull(parse_url($url, PHP_URL_SCHEME));
        self::assertEquals($expectedHost, parse_url($url, PHP_URL_HOST));
        self::assertEquals($expectedPath, parse_url($url, PHP_URL_PATH));
        self::assertEquals($expectedQuery, parse_url($url, PHP_URL_QUERY));

        // missing components are not present in the array
        $valuesArray = parse_url($url);
        self::assertArrayNotHasKey('fragment', $valuesArray);
    }

    public function testBadlyFormedUrls()
    {
        $badUrls = ["http:///example.com", "http://:80", "http://user@:80"];
        foreach ($badUrls as $badUrl) {
            self::assertFalse(parse_url($badUrl));
        }
    }

    /**
     * testReservedChars
     *
     * 'reserved characters' are symbols defined in the syntax of a url.  See http://www.faqs.org/rfcs/rfc3986.html
     * regarding how they should be handled.  There's also a good response on stackoverflow here:
     * https://stackoverflow.com/questions/1547899/which-characters-make-a-url-invalid, which covers
     * reserved characters, risky characters and illegal characters fairly concisely
     *
     */

    public function testReservedChars()
    {
        $reservedChars = ['!', '*', '\'', '(', ')', ':', ';', '@', '&', '=', '+', '$', ',', '/', '?', '#', '[', ']' ];

        // reserved chars in the path component
        $urlString = 'http://www.somehost.com/' . implode($reservedChars);

        $result = parse_url($urlString);

        self::assertEquals('http', $result['scheme']);
        self::assertEquals('www.somehost.com', $result['host']);

        // the '?' which is four characters from the end of the reserved characters array is interpreted as the
        // delimiter for a querystring.

        $expectedResult = '/' . implode(array_slice($reservedChars, 0, array_search('?', $reservedChars)));
        self::assertEquals($expectedResult, parse_url($urlString, PHP_URL_PATH));
    }

    /**
     * testMultibyteCharInPath
     * parse_url can deal with unicode characters in the path
     */
    public function testMultibyteCharInPath()
    {
        // \u{263A} is a smiley face.  Note that in Windows you MUST use double quotes (not single quotes)
        // as the string delimiter in order to use unicode codepoints
        $pathWithMultibyteCharSmileyFace = "/Hello/World" . "\u{263A}";

        // the path has 13 characters: 12 plus the smiley face
        self::assertEquals(13, mb_strlen($pathWithMultibyteCharSmileyFace));

        $urlString = 'http://www.nowhere.com' . $pathWithMultibyteCharSmileyFace;
        $result = parse_url($urlString, PHP_URL_PATH);
        self::assertEquals($pathWithMultibyteCharSmileyFace, $result);
    }

    public function testIllegalCharacters()
    {
        $scheme = 'http://';
        $hostFirstPart = 'no';
        $hostLastPart = 'where.com';
        // "BELL" control character - illegal
        $illegalChar = chr(0x7);

        $illegalUrl =  $scheme . $hostFirstPart. $illegalChar . $hostLastPart;

        // documentation on parse_url says illegal characters are replaced by '_'.
        $expectedHost = $hostFirstPart . '_' . $hostLastPart;
        self::assertEquals($expectedHost, parse_url($illegalUrl, PHP_URL_HOST));
    }

    public function testMultipleQueryParams()
    {
        $urlString = 'http://hostname/some/path?arg1=value1;arg2=value2';
        $expectedQueryValue = 'arg1=value1;arg2=value2';
        $actualQueryValue = parse_url($urlString, PHP_URL_QUERY);
        self::assertEquals($expectedQueryValue, $actualQueryValue);
    }

}
