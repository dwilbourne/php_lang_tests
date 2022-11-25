<?php
declare (strict_types=1);
/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */

namespace tests\exception;


use PHPUnit\Framework\TestCase;

/**
 * Class ReflectionExceptionTest
 */
class ReflectionExceptionTest extends TestCase
{
    public function testReflectionException() : void
    {
        self::expectException(\ReflectionException::class);
        $foo = new \ReflectionClass('invalid argument');
    }
}