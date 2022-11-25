<?php
/**
 * @package: pvc
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version: 1.0
 */

namespace string;

use PHPUnit\Framework\TestCase;

class ExplodeTest extends TestCase {

    function testExplodeMultipleSpaces() {
        $part0 = "thisissometext";
        $part1 = "separatedbyspacesfromthistext";
        $subject = $part0 . "            " . $part1;
        $parts = array_values(array_filter(explode(" ", $subject)));
        self::assertEquals($part0, $parts[0]);
        self::assertEquals($part1, $parts[1]);
    }

    // explode handles multiple spaces but not tabs
    function testExplodeTabs() {
        $part0 = "thisissometext";
        $part1 = "separatedbytabsfromthistext";
        $subject = $part0 . "\t\t\t" . $part1;
        $parts = array_values(array_filter(explode(" ", $subject)));
        self::assertNotEquals($part0, $parts[0]);
    }

    // nor does it handle vertical whitespace
    function testExplodeVerticalWhitespace() {
        $part0 = "thisissometext";
        $part1 = "separatedbytabsfromthistext";
        $subject = $part0 . "\n\n\n" . $part1;
        $parts = array_values(array_filter(explode(" ", $subject)));
        self::assertNotEquals($part0, $parts[0]);
    }

}
