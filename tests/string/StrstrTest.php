<?php
/**
 * @package: pvc
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version: 1.0
 */

namespace tests\string;

use PHPUnit\Framework\TestCase;

class StrstrTest extends TestCase {

    function testStrstr() {
        $haystack = '12345.678';
        $needle = '.';
        $beforeDecimal = strstr($haystack, $needle, true);
        self::assertEquals('12345', $beforeDecimal);
    }
}
