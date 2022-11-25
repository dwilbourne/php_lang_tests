<?php
/**
 * @package: pvc
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version: 1.0
 */

namespace tests\exception;

use Exception;
use PHPUnit\Framework\TestCase;

class ExceptionTest extends TestCase {

    function testNullArguments() {
        // demonstrate that message string can be null at construction time
        $e = new Exception();
        self::assertTrue($e instanceof Exception);
    }

}
