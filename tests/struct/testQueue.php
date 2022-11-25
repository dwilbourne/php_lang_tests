<?php
/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version 1.0
 */

use PHPUnit\Framework\TestCase;

class testQueue extends TestCase {

    protected $queue;

    function setUp() : void {
        $this->queue = new SplQueue();
    }

    /**
     * testIterate
     *
     * beware that this class inherits from SplDoublyLinkedList and so push and pop do NOT behave as you would think.
     * use enqueue / dequeue instead.
     */
    public function testIterate() {

        $this->queue->enqueue('foo');
        $this->queue->enqueue('Bar');
        $this->queue->enqueue('baz');


        self::assertEquals('foo', $this->queue->dequeue());
    }
}
