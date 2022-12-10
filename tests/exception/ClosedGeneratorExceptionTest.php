<?php

declare(strict_types=1);

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */

namespace tests\exception;

use Generator;
use Exception;
use PHPUnit\Framework\TestCase;

/**
 * Class ClosedGeneratorExceptionTest
 *
 * there are two ways of closing a generator: hitting the return statement in the generator function and throwing an
 * exception somewhere in the middle of the generator function.  A few places on the web indicate that accessing a
 * closed generator should generate a ClosedGeneratorException, but the code below indicates that it throws a generic
 * Exception (\Exception::class);
 */
class ClosedGeneratorExceptionTest extends TestCase
{
    public function generateX2Y(int $from, int $to): int|Generator
    {
        if ($from <= 0) {
            throw new Exception("generates only positive integer sequence", 0);
        }
        if ($from > $to) {
            throw new exception("'to' must be greater then 'from' argument.");
        }

        // chr(97) -> 'a'
        $codeForLowerCaseA = 97;
        for ($i = $from, $j = $codeForLowerCaseA; $i <= $to; $i++, $j++) {
            yield chr($j) => $i;
        }
        return $i;
    }

    public function testGetReturnBeforeGeneratorHasReturned(): void
    {
        $from = 5;
        $to = 10;

        $generator = $this->generateX2Y($from, $to);

        // trying to get return value of generator that has not yet returned produces an exception
        self::expectException(Exception::class);
        $generator->getReturn();
    }

    public function testGeneratorVerbsAndClosingViaReturn(): void
    {
        $from = 5;
        $to = 10;
        $actualResult = [];

        $generator = $this->generateX2Y($from, $to);

        // current method returns the first value
        self::assertEquals($from, $generator->current());

        foreach ($generator as $key => $value) {
            if ($value == $from) {
                // $key is "a" - the first key
                self::assertEquals("a", $generator->key());
                // the current method returns the most recently yielded value
                self::assertEquals($value, $generator->current());
            }
            $actualResult[$key] = $value;
        }
        // yielded values are returned as part of iteration but the returned value is not
        $expectedResult = ["a" => 5, "b" => 6, "c" => 7, "d" => 8, "e" => 9, "f" => 10];
        self::assertEquals($expectedResult, $actualResult);

        // generator is closed but "alive"
        // when closed, the current method returns null
        self::assertNull($generator->current());
        // return value is $to + 1
        self::assertEquals($to + 1, $generator->getReturn());
        // generator is closed
        self::assertFalse($generator->valid());

        // this should be a closed generator exception I think
        self::expectException(Exception::class);
        foreach ($generator as $y) {
            $someArray[] = $y;
        }
    }

    public function testClosedViaException(): void
    {
        $from = -2;
        $to = 7;

        // exception is not generated until you actually use the generator
        $generator = $this->generateX2Y($from, $to);

        try {
            foreach ($generator as $key => $value) {
                $resultArray[$key] = $value;
            }
        } catch (Exception $e) {
            // do nothing, but generator should now be closed
        }
        self::assertFalse($generator->valid());
    }

    /**
     * @throws Exception
     * TODO finish demonstrating how rewind works
     */
    public function testRewind(): void
    {
        $from = 1;
        $to = 5;
        $firstIteration = true;

        $generator = $this->generateX2Y($from, $to);
        foreach ($generator as $key => $value) {
            if ($value == 3 && $firstIteration == true) {
                $generator->rewind();
                $firstIteration = false;
            }
            // throw the keys away - keys are demonstrated above
            $resultArray[] = $value;
        }
        $expectedResult = [1, 2, 1, 2, 3, 4, 5];
        self::assertEquals($expectedResult, $resultArray);
    }

	/**
	 * testNext
	 * TODO finished demonstrating how next works
	 */
	public function testNext() : void
	{

	}

	/**
	 * testSend
	 * TODO finish demonstrating how send works
	 */
	public function testSend() : void
	{

	}
}
