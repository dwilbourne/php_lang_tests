<?php

namespace tests\type;

use PHPUnit\Framework\TestCase;


class IsIntegerTest extends TestCase {


	// is 123.0 an integer? No
	function testIsInteger() {
        $this->assertFalse(is_integer(123.0));
    }
	
}

?>