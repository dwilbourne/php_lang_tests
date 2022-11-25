<?php
/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version 1.0
 */

namespace tests\regex;

use PHPUnit\Framework\TestCase;

class EscapeSequencesTest extends TestCase
{

    public function testWord() : void
    {
        $pattern = '/\w+/';
        self::assertEquals(1, preg_match($pattern, 'abc'));
    }
}
