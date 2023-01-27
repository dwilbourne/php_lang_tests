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

    public function testReflectClassWithNoNamespace(): void
    {
        /**
         * you have to require the file manually: classes without namespaces will not autoload unless you modify the
         * autoload section of composer.json.
         */
        require 'tests/reflection/fixtures/ClassWithNoNamespace.php';
        $class = new \ClassWithNoNamespace();
        $reflection = new \ReflectionClass($class);
        self::assertInstanceOf(\ReflectionClass::class, $reflection);
    }
}