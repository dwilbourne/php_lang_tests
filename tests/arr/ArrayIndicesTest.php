<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */
declare (strict_types=1);

namespace tests\arr;

use PHPUnit\Framework\TestCase;

class ArrayIndicesTest extends TestCase
{
    public function testIndices(): void
    {
        $testArray = [
            'nodeId' => 2,
            'parentId' => 1,
            'treeid' => 0,
            'value' => 'foo',
        ];

        /**
         * indices are either strings or numeric.  You cannot access an element with an associative index based on
         * its ordinal position
         */
        self::assertTrue(!isset($testArray[0]));
    }
}
