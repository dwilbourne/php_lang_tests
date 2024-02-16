<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */

declare (strict_types=1);

namespace tests\explode;

use PHPUnit\Framework\TestCase;

class ExplodeTest extends TestCase
{
    public function testExplode(): void
    {
        $separator = '=';
        $testString = 'a=';
        $result = explode($separator, $testString);
        self::assertEquals(2, count($result));
        self::assertEmpty($result[1]);
        self::assertIsString($result[1]);

        $testString = '=a';
        $result = explode($separator, $testString);
        self::assertEquals(2, count($result));
        self::assertEmpty($result[0]);
        self::assertIsString($result[0]);

        $testString = 'a';
        $result = explode($separator, $testString);
        self::assertEquals(1, count($result));
    }
}
