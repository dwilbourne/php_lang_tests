<?php

namespace tests;

use PHPUnit\Framework\TestCase;
use tests\fixtures\Foo;


class AttributeInitializationTest extends TestCase {


	// trying to retrieve an unset attribute returns null and does not result in an exception
	function testGetUninitializedAttribute() {
        $fixture = new Foo();
        $this->assertTrue(empty($fixture->getBar()));
    }
	
}

?>