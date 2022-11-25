<?php
/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version 1.0
 */

use PHPUnit\Framework\TestCase;
use tests\oo\overload\Bar;

class FooBarTest extends TestCase
{

    public function testFooBar() : void {
        $bar = new Bar();
        $bar->baz('some string');
        self::assertTrue(true);
    }
}
