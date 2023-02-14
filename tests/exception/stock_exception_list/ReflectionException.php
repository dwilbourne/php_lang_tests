<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */

declare(strict_types=1);

namespace pvc\err\stock;

/**
 * Reflection exceptions are thrown when you try to reflect something that cannot be reflected (e.g. not an object)
 *
 *
 * Example:
 *
 *    $r = new ReflectionClass("foo");
 *
 */
class ReflectionException extends Exception
{
}
