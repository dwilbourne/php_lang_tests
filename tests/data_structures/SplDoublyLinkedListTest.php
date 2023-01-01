<?php
/**
 * @package: pvc
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version: 1.0
 */

namespace tests\data_structures;

use PHPUnit\Framework\TestCase;

class SplDoublyLinkedListTest extends TestCase {


    protected $list;

    function setUp() : void {
        $this->list = new \SplDoublyLinkedList();
    }

    function testBasicOps() {
        $this->list->add(0, 'a');
        $this->list->add(1, 'c');
        $this->list->add(1, 'b');

		// b is inserted ahead of c
        $this->assertEquals('b', $this->list->offsetGet(1));
        $this->assertEquals('c', $this->list->offsetGet(2));

        $this->assertEquals(3, count($this->list));

		// remove b
        $this->list->offsetUnset(1);
		// list has 2 elements, not 3
        $this->assertFalse($this->list->offsetExists(2));
		// index of c has gone from 2 to 1, e.g. list has been reindexed
        $this->assertEquals('c', $this->list->offsetGet(1));

        // offsetSet behaves like 'update', not like 'add'
        $this->list->offsetSet(1, 'b');
        $this->assertEquals('b', $this->list->offsetGet(1));
        $this->assertFalse($this->list->offsetExists(2));
    }

	// indices must be integers
    function testBadIndex1() {
        $this->expectException('TypeError');
        $this->list->add('a', 'value');
    }

    function testBadIndex2() {
        // notice that it does not do the 'intuitive' thing and change a large index to what should be the
        // the next sequential index
        $this->expectException('OutOfRangeException');
        $this->list->add(433, 'a');
    }

    function testOffsetSet() {
        // offsetSet behaves differently in this data structure than the way I would expect.  It is a
        // pure update and cannot be used to add elements to the list (use add or push instead)
        $this->expectException('OutOfRangeException');
        $this->list[0] = 'a';

        $this->list->add(0, 'a');
        // calling offsetSet
        $this->list[0] = 'b';

    }

    function testPush() {
        $this->list->push('a');
        $this->list->push('b');
        $this->list->push('c');

        $this->assertEquals($this->list->offsetGet(0), 'a');
        $this->assertEquals($this->list->offsetGet(1), 'b');
        $this->assertEquals($this->list->offsetGet(2), 'c');

    }

}
