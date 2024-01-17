<?php

declare (strict_types=1);
/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */

namespace tests\arr;

use PHPUnit\Framework\TestCase;

class ArrayDiffTest extends TestCase
{
    public function testArrayDiff() : void
    {
        $array1 = ['red', 'green', 'blue'];
        $array2 = ['red', 'green',];
        self::assertEquals([2 => 'blue'], array_diff($array1, $array2));
    }

}
