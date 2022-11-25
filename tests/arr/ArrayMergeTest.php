<?php
/**
 * @package: pvc
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version: 1.0
 */

namespace tests\arr;

use PHPUnit\Framework\TestCase;

class ArrayMergeTest extends TestCase {

    function testArrayMergeWithNonArrayArgs() {
        // all arguments must be arrays
        self::expectError();
        self::assertFalse(is_array(array_merge('b', array('c', 'd'))));
    }

}
