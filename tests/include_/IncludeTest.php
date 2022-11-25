<?php

declare (strict_types=1);
/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */

namespace tests\include_;

use PHPUnit\Framework\TestCase;

class IncludeTest extends TestCase
{
    protected string $fixturesDir;

    public function setUp() : void
    {
        $this->fixturesDir = __DIR__ . DIRECTORY_SEPARATOR . "fixtures" . DIRECTORY_SEPARATOR;
    }

    public function testReturn() : void
    {
        // include file returns the string 'PHP'
        $file = $this->fixturesDir . "return.php";
        self::assertEquals('PHP', include($file));
    }

    public function testNoReturn() : void
    {
        $var = "";
        // include file sets the value of $var to 'PHP';
        $file = $this->fixturesDir . "noreturn.php";
        self::assertEquals(true, include($file));
        echo $var . "is great";
    }

    public function testBadPhpFile() : void
    {
        $file = $this->fixturesDir . "badPhpFile.php";
        self::expectException("ParseError");
        include($file);
    }
}
