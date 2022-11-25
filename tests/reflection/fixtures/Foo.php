<?php

declare (strict_types=1);
/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */

namespace tests\reflection\fixtures;


/**
 * Class Foo
 */
class Foo
{
    protected Bar $barFirst;
    protected Bar $barSecond;
    protected string $notBar;
}