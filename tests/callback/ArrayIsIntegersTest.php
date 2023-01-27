<?php

declare (strict_types=1);
/**
 * @package {PROJECT_NAME}
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */

namespace tests\callback;

use PHPUnit\Framework\TestCase;

class ArrayIsIntegersTest extends TestCase
{
    protected function dataProvider() : array
    {
        return [
          [[1, 2, 3, 4], true],
          [[1, 2, "foo"], false],
        ];
    }

    /**
     * testArrayIsIntegers
     * @param array $array
     * @param $expectedResult
     * @dataProvider dataProvider
     */
    public function testArrayIsIntegers(array $array, $expectedResult) : void
    {
        $callback = function($carry, $x) { return ($carry && is_int($x)); };
        self::assertEquals($expectedResult, array_reduce($array, $callback, !empty($array)));
    }
}
