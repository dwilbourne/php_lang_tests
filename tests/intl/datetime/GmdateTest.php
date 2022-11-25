<?php
/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version 1.0
 */

namespace intl\datetime;

use PHPUnit\Framework\TestCase;

class GmdateTest extends TestCase {


    public function testGmdate() {
        $input = 3 * 60 * 60;
        $expectedResult = '03:00';
        $format = 'H:i';
        self::assertEquals($expectedResult, gmdate($format, $input));
    }
}
