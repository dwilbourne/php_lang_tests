<?php
namespace tests\oo\overload;
/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version 1.0
 */

/**
 * Class foo
 */
class Foo
{
    protected int $variable;

    public function baz(int $x) : void
    {
        $this->variable = $x;
    }

}