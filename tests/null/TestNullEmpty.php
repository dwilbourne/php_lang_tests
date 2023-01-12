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

	public function testNotInitializedProperty() : void
	{
		$fixture = new Fixture();
		$this->expectException(\Error::class);
		$foo = $fixture->getFoo();
	}

	public function testInitializeAndThenSetToNull() : void
	{
		$fixture = new Fixture();
		$testString = 'some string';
		$fixture->setFoo($testString);
		self::assertEquals($testString, $fixture->getFoo());

		$fixture->setFoo(null);
		self::assertNull($fixture->getFoo());
	}

}
