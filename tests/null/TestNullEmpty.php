<?php
/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version 1.0
 */

namespace tests\null;

use PHPUnit\Framework\TestCase;

class TestNullEmpty extends TestCase
{

    public function testIsNull()
    {
        // empty string is not null
        self::assertFalse(is_null(''));
        // 0 is not null
        self::assertFalse(is_null(0));
    }

    public function testEmpty()
    {
        self::assertTrue(empty(null));
        self::assertTrue(empty(''));
        self::assertTrue(empty([]));
        self::assertTrue(empty(0));
    }

}
