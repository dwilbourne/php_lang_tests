<?php
/**
 * @package: pvc
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version: 1.0
 */

namespace intl\datetime;

use DateTimeImmutable;
use DateTimeZone;
use PHPUnit\Framework\TestCase;

class DateTimeTest extends TestCase {

    function testCreation() {

        $tz = new DateTimeZone('America/New_York');
        $dt = new DateTimeImmutable('2020-5-10', $tz);
        echo 'DateTime = ' . $dt->format('Y-m-d H:i:s') . PHP_EOL;
        // if $dt was not immutable, the time component of $dt would also be set to 0
        $dtWithoutTime = $dt->setTime(0, 0, 0);
        echo 'DateTimeWithoutTime = ' . $dt->format('Y-m-d H:i:s') . PHP_EOL;
        $creationTimeInSeconds = $dt->getTimestamp() - $dtWithoutTime->getTimestamp();
        echo 'this test was run at ' . $dt->format('H:i:s');
        // you would have to run this test at exactly midnight and be lucky for this test to fail.
        self::assertTrue($creationTimeInSeconds > 0);

    }

    function testSmallestLargestDateTime() {
        // smallest date
        $input = '-9999-01-01 00:00:00';
        $dt = new \DateTime($input);
        self::assertEquals($input, $dt->format('Y-m-d H:i:s'));
        echo 'smallest date as timestamp = ' . $dt->getTimestamp() . PHP_EOL;
        $input = '-10000-12-31';
        $dt = new \DateTime($input);
        /**
         * hah!  as of php 8, this test fails now - looks like they fixed the DateTime object......
         */
        self::assertEquals('2000-12-31', $dt->format('Y-m-d'));

        // largest date
        $input = '9999-12-31 23:59:59';
        $dt = new \DateTime($input);
        self::assertEquals($input, $dt->format('Y-m-d H:i:s'));
        echo 'largest date as timestamp = ' . $dt->getTimestamp() . PHP_EOL;
        $input = '10000-01-01';
        $dt = new \DateTime($input);
        self::assertEquals('2000-01-01', $dt->format('Y-m-d'));

        // try modifying DateTimeImmutable
        $input = '9999-12-31 23:59:59';
        $dt = new \DateTimeImmutable($input);
        $expectedResult = '10000-01-01';
        self::assertEquals($expectedResult, $dt->modify('+1 day')->format('Y-m-d'));
    }

    function testTimeZoneComparison() {

        $tzNy = new DateTimeZone('America/New_York');
        $dtNy = new DateTimeImmutable('2020-5-10', $tzNy);
        $tsNy = $dtNy->getTimestamp();

        $tzUtc = new DateTimeZone('UTC');
        $dtUtc = new DateTimeImmutable('2020-5-10', $tzUtc);
        $tsUtc = $dtUtc->getTimestamp();

        /**
         * there is a 4 hour time difference between New York and Greenwich (UTC) on this date.  Note that you MUST
         * convert to timestamps (e.g. UTC) in order to make the calculation
         */
        $interval = -4 * 60 * 60;
        self::assertEquals($interval, $tzNy->getOffset($dtNy));
        self::assertEquals($interval, $tsUtc - $tsNy);

        /**
         * what do you get if you just compare one datetime from another?  The comparison is not timezone sensitive
         * either: have to convert to timestamp
         */
        self::assertFalse($dtNy < $dtUtc);

    }


}
