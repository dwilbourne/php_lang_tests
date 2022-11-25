<?php

declare (strict_types=1);
/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */

namespace tests\json;

use PHPUnit\Framework\TestCase;

class JsonDecodeTest extends TestCase
{
    protected string $filePath;
    protected string $fileContents;

    public function setUp() : void
    {
    }

    public function testPersonsToArray() : void
    {
        $this->filePath = './tests/json/fixture/persons.json';
        $this->fileContents = file_get_contents($this->filePath);

        $array = json_decode($this->fileContents, true);
        // three people in the array
        self::assertEquals(3, count($array));
        // no associative indices for these 3 elements
        $firstElement = $array[0];
        // first element of the array is an array of 5 elements
        self::assertEquals(5, count($firstElement));
    }

    public function testMessagesToArray() : void
    {
        $this->filePath = './tests/json/fixture/messages.json';
        $this->fileContents = file_get_contents($this->filePath);

        $array = json_decode($this->fileContents, true);
        // 6 messages in the array
        self::assertEquals(6, count($array));
    }
}
