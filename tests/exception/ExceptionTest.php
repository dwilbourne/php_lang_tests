<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */

declare(strict_types=1);

namespace tests\exception;

use Exception;
use PHPUnit\Framework\TestCase;

/**
 * Class ExceptionTest
 *
 *
 * As of 12/9/22, the following hierarchy is fairly current with respect to the exceptions that can be triggered.
 * The class below tests some of these.  The errors that have tests are followed by an asterisk (*)
 *
 * @formatter:off
 *
 * Throwable
    └── Exception
        ├── ClosedGeneratorException
        ├── DOMException
        ├── ErrorException
        ├── IntlException
        ├── JsonException
        ├── LogicException
        │       ├── BadFunctionCallException
        │       │        └── BadMethodCallException
        │       ├── DomainException
        │       ├── InvalidArgumentException
        │       ├── LengthException
        │       └── OutOfRangeException
        ├── PharException
        ├── ReflectionException
        ├── RuntimeException
        │       ├── OutOfBoundsException
        │       ├── OverflowException
        │       ├── PDOException
        │       ├── RangeException
        │       ├── UnderflowException
        │       ├── UnexpectedValueException
        │       └── PDOException
        ├── SodiumException
        ├── FiberExit
        └── Random\RandomException
 *
 * @formatter:on
 */
class ExceptionTest extends TestCase
{
    /**
     * testNullArguments
     *
     * demonstrate that message string can be null at construction time
     */
    public function testNullArguments()
    {
        $e = new Exception();
        self::assertTrue($e instanceof Exception);
    }
}
