<?php
/**
 * @package: pvc
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version: 1.0
 */

namespace tests\intl\datetime;

use PHPUnit\Framework\TestCase;

class TimestampTest extends TestCase {

    // demonstrate that timestamp of 0 is the stroke of midnight on 1/1/70 Greenwich Mean Time.
    // gmdate is for a GMT date, the date verb is for a local date (e.g. in your current timezone)
    function testTimestampZero() {
        $ts = 0;
        $expectedResult = '01/01/1970 00:00:00';
        $fmt = 'm/d/Y H:i:s';
        self::assertEquals($expectedResult, gmdate($fmt, $ts));
    }

    // because timestamp is typed as an integer, the upper limit of timestamp is dependent on PHP_INT_MAX
    function testTimeStampMinMax() {
        $ts = PHP_INT_MAX;
        $expectedResult = '12/04/292277026596 15:30:07';
        $fmt = 'm/d/Y H:i:s';
        self::assertEquals($expectedResult, gmdate($fmt, $ts));

        $ts = PHP_INT_MIN;
        $expectedResult = '01/27/-292277022657 08:29:52';
        $fmt = 'm/d/Y H:i:s';
        self::assertEquals($expectedResult, gmdate($fmt, $ts));
    }

}
