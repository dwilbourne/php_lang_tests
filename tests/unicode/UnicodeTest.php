<?php

declare (strict_types=1);
/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */

namespace tests\unicode;

use PHPUnit\Framework\TestCase;

class UnicodeTest extends TestCase
{
	public function testCreateUnicodeCharAsString() : void
	{
		// euro symbol followed by some numbers
		$string = "\u{20AC}" . "123";
	}
}
