<?php
/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version 1.0
 */

namespace tests\regex;


use PHPUnit\Framework\TestCase;

class PregMatchTest extends TestCase
{
    function testPregMatchNegation()
    {
        $pattern = '/^[^a-zA-Z_\x80-\xff]$/';
        $subject = "##";
        // preg_match returns 0 if the subject does not match the pattern, 1 if it does.
        // this subject matches, e.g. # is not in the character class [a-zA-Z_\x80-\xff]
        self::assertEquals(1, preg_match($pattern, $subject));
    }

}