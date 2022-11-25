<?php
/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version 1.0
 */

namespace tests\numeric;

use PHPUnit\Framework\TestCase;

class TestModulus extends TestCase
{
    public function testModulusOnNegativeNumber() {
        // modulus remains negative, not an absolute value
        self::assertEquals(-2, -2 % 10);
    }
}
