<?php

declare (strict_types=1);
/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */

namespace tests\reflection;


use PHPUnit\Framework\TestCase;
use tests\reflection\fixtures\Foo;

/**
 * Class RelectionTest
 */
class ReflectionTest extends TestCase
{
    public function testGetFooAttributes() : void
    {
        $foo = new Foo();
        $reflection = new \ReflectionClass($foo);
        $properties = $reflection->getProperties();
        foreach ($properties as $reflectionProperty) {
            $type = $reflectionProperty->getType();
            echo $type . "\n";
        }
        self::assertTrue(true);
    }
}