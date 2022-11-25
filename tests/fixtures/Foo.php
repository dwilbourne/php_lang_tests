<?php
/**
 * @package: pvc
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version: 1.0
 */

namespace tests\fixtures;


/**
 * Class Foo
 */
class Foo {

    protected $bar;

    function setBar(string $var) {
        $this->bar = $var;
    }

    function getBar() {
        return $this->bar;
    }

}