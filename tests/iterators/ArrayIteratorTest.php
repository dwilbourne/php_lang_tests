<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */

declare (strict_types=1);

namespace tests\iterators;

use ArrayIterator;
use PHPUnit\Framework\TestCase;
use stdClass;

class ArrayIteratorTest extends TestCase
{
    protected ArrayIterator $arrayIterator;

    public function setUp(): void
    {
        $this->arrayIterator = new ArrayIterator();
    }

    public function testConstruct(): void
    {
        self::assertInstanceOf(\Iterator::class, $this->arrayIterator);
    }

    public function testOffsetSet(): void
    {
        $element = new stdClass();
        $this->arrayIterator[] = $element;
        self::assertEquals(1, count($this->arrayIterator));
        self::assertEquals(0, $this->arrayIterator->key());
        self::assertEquals($element, $this->arrayIterator->current());
    }

    public function testCallByValue(): void
    {
        $testArray = [1, 2, 3, 4];
        /**
         * call by value
         */
        $iteratorA = new ArrayIterator($testArray);
        $iteratorA[0] = 'foo';
        self::assertEquals(1, $testArray[0]);
    }
}
