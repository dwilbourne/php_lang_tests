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
}