<?php
declare (strict_types=1);
/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */

namespace tests\exception;


use PHPUnit\Framework\TestCase;

/**
 * Class OutOfRangeExceptionTest
 */
class OutOfRangeExceptionTest extends TestCase
{
    // not sure how to create this exception or under what circumstances you would throw it
    public function testOutOfRangeException() : void
    {
        $this->expectError(\Error::class);
        $array = [1, 2, 3, 4];
        $z = $array[5];
    }
}