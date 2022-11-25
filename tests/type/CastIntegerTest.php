<?php
/**
 * @package: pvc
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version: 1.0
 */

namespace tests\type;

use PHPUnit\Framework\TestCase;

class CastIntegerTest extends TestCase {

    // is casting various types to integer
    function testCastInteger() {
        $this->assertEquals(12345, (int) "12345");
        $this->assertEquals(0, (int) "abc");
    }

}
