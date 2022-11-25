<?php
namespace tests\oo\overload;
/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version 1.0
 */

/**
 * Class Bar
 */
class Bar extends Foo
{

    public function baz(string $x) : void
    {
        $this->variable = $x;
    }
}