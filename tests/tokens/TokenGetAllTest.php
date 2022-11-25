<?php
/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version 1.0
 */

use PHPUnit\Framework\TestCase;

class TokenGetAllTest extends TestCase
{

    public function testTokenGetAll() {
        $string = file_get_contents(__FILE__);
        $tokens = token_get_all($string);
        self::assertTrue(true);
    }
}
