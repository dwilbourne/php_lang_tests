<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */

declare(strict_types=1);

namespace tests\generator;

use PHPUnit\Framework\TestCase;

class GeneratorTest extends TestCase
{
    public function generateX2Y(int $from, int $to)
    {
        for ($i = $from; $i <= $to; $i++) {
            yield $i;
        }
    }

    public function testGenerator(): void
    {
        $from = 5;
        $to = 10;
        $actualResult = [];

        $generator = $this->generateX2Y($from, $to);
        foreach ($generator as $x) {
            $actualResult[] = $x;
        }
        $expectedResult = [5, 6, 7, 8, 9, 10];
        self::assertEquals($expectedResult, $actualResult);
    }
}
