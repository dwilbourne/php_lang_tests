<?php
/**
 * @package: pvc
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version: 1.0
 */

namespace tests\intl\datetime;


use PHPUnit\Framework\TestCase;

/**
 * Class TimeZoneDocumentor
 */
class TimeZoneDocumentorTest extends TestCase {


    public function testDisplayTimeZones() {
        $tzArray = timezone_identifiers_list();
        foreach($tzArray as $tzId => $tz) {
            echo $tzId . ' => ' . $tz . PHP_EOL;
        }
        self::assertTrue(true);
    }

    function testCreateTimezone() {
        $tzParis = new \DateTimeZone('Europe/Berlin');
        self::assertTrue($tzParis instanceof \DateTimeZone);
    }

    /**
     * testTimezoneOffset
     * @throws \Exception
     * @dataProvider timezoneOffsetDataProvider
     */
    public function testTimezoneOffset(string $offset, int $expectedOffset, string $comment)
    {

        $tz = new \DateTimeZone($offset);
        $dt = new \DateTime('2000-01-01', $tz);
        self::assertEquals($expectedOffset, $tz->getOffset($dt), $comment);
    }

    public function timezoneOffsetDataProvider(): array
    {
        return [

          ['-8:00', -8*60*60, 'standard kind of an offset'],
          ['-8:59', (-8*60-59)*60, 'handles minutes of offset as well as integer hours'],
          ['-12:00', -12*60*60, 'max negative offset to gmt'],
          ['-13:00', -13*60*60, 'should wrap around to a one hour positive offset but does not'],
            ['+13:00', 13*60*60, 'should wrap to one hour negative but does not'],
            ['+40:00', 40*60*60, 'a ridiculous timezone'],
        ];
    }

    public function testDateTimeWithLargeNegativeTimezone(): void
    {
        $offset = '-40:00';
        $tz = new \DateTimeZone($offset);
        $input = '-9999-01-01 00:00:00';
        $dt = new \DateTime($input, $tz);
        self::assertEquals($input, $dt->format('Y-m-d H:i:s'));

        $offset = '-39:00';
        $tz = new \DateTimeZone($offset);
        $input = '-9999-01-01 00:00:00';
        $dt2 = new \DateTime($input, $tz);

        $absolute = true;
        $interval = $dt2->diff($dt, $absolute);
        /**
         * is not sensitive to the timezone offset!
         */
        self::assertEquals('0', $interval->format('%s'));
        /**
         * convert to utc and it works.  subtle..........
         */
        self::assertEquals('3600', $dt->getTimestamp() - $dt2->getTimestamp());
    }

    public function testDateTimeWithTimezoneEmbeddedInFirstArg(): void
    {
        $input = '2000-01-01T00:00:00+00:00';
        $dt = new \DateTime($input);

        $input = '2000-01-01T00:00:00-1:00';
        $dt2 = new \DateTime($input);

        $interval = $dt2->diff($dt);
        /**
         * still not sensitive to the timezone offset!
         */
        self::assertEquals('0', $interval->format('%s'));
        /**
         * have to convert to utc
         */
        self::assertEquals('-3600', $dt->getTimestamp() - $dt2->getTimestamp());
    }
}