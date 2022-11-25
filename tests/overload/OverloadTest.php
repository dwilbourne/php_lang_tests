<?php
/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version 1.0
 */

namespace tests\overload;

function is_readable(string $dir) : bool {
    return false;
}

use PHPUnit\Framework\TestCase;

/**
 * Class OverloadTest
 * @package tests\overload
 */
class OverloadTest extends TestCase
{
    function testOverloadIsReadable()
    {
        $dir = __DIR__;

        self::assertFalse(is_readable($dir));

    }
}