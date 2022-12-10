<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */

declare(strict_types=1);

namespace tests\error;

use tests\error\fixture\ArgumentCountErrorFixture;
use PHPUnit\Framework\TestCase;

/**
 * I don't know of an instance where PHP will throw an instance of the base class ('\Error').  If there is one,
 * it will be good to document it more clearly below in the tests.
 */

/**
 * Class ErrorTest
 *
 * As of 12/9/22, the following hierarchy is fairly current with respect to the errors that can be triggered. The class
 * below tests some of these.  The errors that have tests are followed by an asterisk (*)
 *
 * @formatter:off
 *
 * Throwable
        ├── Error
            ├── ArithmeticError *
            │       └── DivisionByZeroError *
            ├── AssertionError *
            ├── CompileError
            │       └── ParseError *
            ├── TypeError *
            │   └── ArgumentCountError *
            ├── ValueError
            ├── UnhandledMatchError
            ├── FiberError
            └── Random\RandomError
                    └── BrokenRandomEngineError
 *
 * @formatter:on
 */

class ErrorTest extends TestCase
{
    /**
     * examples of expressions that cause an arithmetic error:
     *
     *    intdiv(PHP_INT_MIN, -1)        // integer division where the result is not / cannot be an integer
     *    4 << -2                                        // trying to bitshift by a negative number of places
     *
     */

    public function testArithmeticError_1()
    {
        $this->expectException('\ArithmeticError');
        $c = intdiv(PHP_INT_MIN, -1);
    }

    public function testArithmeticError_2()
    {


        $this->expectException('\ArithmeticError');
        $c = 4 << -2;
    }

    /**
     * assertion errors are thrown "if an assertion fails".  Assertion errors are available since PHP 7.
     *
     *    assert(true == false)
     *
     * Note, however, that there is quite a bit of environmental massage that is required in order for
     * the error to actually be thrown.  A full but confusing synopsis can be found here.  And unfortunately, the
     * settings discussed in that blog do not match the settings in the ini file that I am using as I write this test
     * using PHP 7.1.30.
     *
     * https://airbrake.io/blog/php-exception-handling/assertionerror
     *
     * The cleanest way I can find to actually generate the error (and it is a total hack) is to use one of the
     * assert_options called ASSERT_CALLBACK which identifies a callback that runs in the event the assertion fails.
     * This method works only if zend.assertions = 1 and assert.exception = on in the php ini file.
     */

    public function onAssertionFailureCallback()
    {
        $e = new \AssertionError();
        throw $e;
    }

    public function testAssertionError()
    {
        $this->expectException('\AssertionError');
        assert_options(ASSERT_CALLBACK, 'onAssertionFailureCallback');
        assert(true == false);
    }

    /**
     * @function testDivisionByZeroErrorModulus
     *
     * modulus function throws DivisionByZeroError as expected
     */
    public function testDivisionByZeroErrorModulus()
    {
        $this->expectException('\DivisionByZeroError');
        $c = 5 % 0;
    }

    /**
     * @function testDivisionByZeroErrorIntDiv
     *
     * intdiv function throws DivisionByZeroError as expected
     *
     */
    public function testDivisionByZeroErrorIntDiv()
    {
        $this->expectException('\DivisionByZeroError');
        $c = intdiv(5, 0);
    }

    /**
     * @function testDivisionByZeroRegularDiv
     *
     * PHP issues a warning not an error for standard divide by zero.
     *
     */
    public function testDivisionByZeroRegularDiv()
    {
        set_error_handler(function () {
            throw new \DivisionByZeroError();
        });
        $this->expectException('\DivisionByZeroError');
        $c = 5 / 0;
        restore_error_handler();
    }

    public function testDivisionByZeroErrorIsArithmeticError()
    {
        try {
            $c = intdiv(5, 0);
        } catch (\DivisionByZeroError $e) {
            self::assertTrue($e instanceof \ArithmeticError);
        }
    }

    public function testParseError()
    {
        $this->expectException('\ParseError');
        eval('if(1 >');
    }

    /**
     * According to the PHP documentation:
     * -----------------------
     * There are three scenarios where a TypeError may be thrown. The first is where the argument type being passed
     * to a function does not match its corresponding declared parameter type. The second is where a value being
     * returned from a function does not match the declared function return type. The third is where an invalid
     * number of arguments are passed to a built-in PHP function (strict mode only).  See the InvalidArgumentCountError
     * above for testing the third scenario described.
     * -----------------------
    */

    public function testTypeErrorBadArgumentType()
    {
        $closure = function (string $a) {
            return $a;
        };
        $this->expectException('\TypeError');
        // note the importance of strict_types = 1 at the top of this file for this call.  Without it,
        // PHP will cast the number 9 to a string.
        $b = $closure(9);
    }

    public function testTypeErrorBadReturnType()
    {
        $closure = function (string $a): int {
            return $a;
        };
        $this->expectException('\TypeError');
        $b = $closure('a');
    }

    /**
     * argumentcount errors are thrown when too few or too many arguments are passed to a function or method.  This
     * error is an extension of TypeError
     *
     * function foo($x, $y, $z) { }
     * foo(1);    // throws the error
     * function foo($x=1, $y) { }
     * foo("Bar"); // throws the error - note the backwards declaration of the optional argument
     * function foo($x, $y=1) { echo "OK"; }
     * foo("Bar");    // this does not throw an error
     *
     */

    public function testArgumentCountErrorUserDefinedFunction()
    {

        $fixture = new ArgumentCountErrorFixture();
        $this->expectException('\ArgumentCountError');
        $fixture->foo(1);
    }

    public function testArgumentCountErrorBuiltInFunction()
    {
        $this->expectException('\ArgumentCountError');
        $c = strlen('abcde', 9);
    }

    public function testArgumentCountErrorIsTypeError()
    {
        try {
            $c = strlen('abcde', 9);
        } catch (\TypeError $e) {
            self::assertTrue($e instanceof \TypeError);
        }
    }
}
