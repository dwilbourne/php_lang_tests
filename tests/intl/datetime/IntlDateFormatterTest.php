<?php

namespace tests\intl\datetime;

use PHPUnit\Framework\TestCase;
use IntlDateFormatter;

/**
 * Class IntlDateTimeShortDateParsingTest
 *
 * The ICU documentation for this class is quite good.  It can be found here: http://www.icu-project.org/apiref/icu4c/classSimpleDateFormat.html#details
 *
 */

class IntlDateFormatterTest extends TestCase {

    
	function testDefaults() {
        
        $frmtr = new \IntlDateFormatter('en-US', IntlDateFormatter::SHORT, IntlDateFormatter::NONE, 'America/New_York', IntlDateFormatter::GREGORIAN);
        
        // default format from constructor
        $expectedResult = 'M/d/yy';
        $this->assertEquals($expectedResult, $frmtr->getPattern());

        /**
         * test IntlDateFormatter against standard DateTime object as well as strtotime.  the parse method returns a timestamp (an integer).  The timestamp
         */

        // get the current date/time in a DateTime object
        $dtNow = new \DateTimeImmutable();

        // make sure to zero out the time portion. Note that this require an explicit set because $dtNow is immutable.  In other words
        // $dtNow->setTime(0, 0, 0) does not change the value of $dtNow.  It returns a new object based on $dtNow with the time set to 0.
        $dtNow = $dtNow->setTime(0, 0, 0);

        // output in a US style format with a 4 digit year for the test.
        $stringNow = $dtNow->format('m/d/Y');
        // on July 9, 2019 stringNow equals '07/09/2019'

        // Note that DateTime and IntlDateTime use different patterns
        $frmtr->setPattern('MM/dd/yyyy');

        // check against strtotime which takes a string and creates a timestamp (integer)
        $this->assertEquals(strtotime($stringNow), $frmtr->parse($stringNow));

        // check against DateTime::getTimestamp()
        $expectedResult = $dtNow->getTimestamp();
        $this->assertEquals($expectedResult, $frmtr->parse($stringNow));

	}

    /**
     * Here is the ICU documentation on the year portion ('yy') of the pattern.
     *
     * When parsing a date string using the abbreviated year pattern ("y" or "yy"), SimpleDateFormat must interpret the abbreviated year relative to some century.
     * It does this by adjusting dates to be within 80 years before and 20 years after the time the SimpleDateFormat instance is created. For example, using a
     * pattern of "MM/dd/yy" and a SimpleDateFormat instance created on Jan 1, 1997, the string "01/11/12" would be interpreted as Jan 11, 2012 while the string
     * "05/04/64" would be interpreted as May 4, 1964. During parsing, only strings consisting of exactly two digits, as defined by Unicode::isDigit(), will be
     * parsed into the default century. Any other numeric string, such as a one digit string, a three or more digit string, or a two digit string that isn't all
     * digits (for example, "-1"), is interpreted literally. So "01/02/3" or "01/02/003" are parsed (for the Gregorian calendar), using the same pattern,
     * as Jan 2, 3 AD. Likewise (but only in lenient parse mode, the default) "01/02/-3" is parsed as Jan 2, 4 BC.
     *
     * If the year pattern has more than two 'y' characters, the year is interpreted literally, regardless of the number of digits. So using the pattern "MM/dd/yyyy",
     * "01/11/12" parses to Jan 11, 12 A.D.
     */


    /**
     * @dataProvider intervalProvider
     */
	function testEightyTwentyRule(string $interval0, string $interval1) {

	    // $this->markTestSkipped('Cannot thoroughly test IntlDateFormatter until "wrong timestamp" bug is fixed.');

        // so the algorithm is to take the first argument and add it to the current date,
        // format that date as a string with a two digit year, and parse that date using the international date formatter,
        // which yields a timestamp.  Then we take the second argument and add it to the current date and then
        // turn that into a timestamp directly from the DateTime object.  The timestamps should be equal.

        $frmtr = new \IntlDateFormatter('en-US', IntlDateFormatter::SHORT, IntlDateFormatter::NONE, 'America/New_York', IntlDateFormatter::GREGORIAN);

        $d0 = $this->adjustDateTimeFromNow($interval0);
        $dateStr = $d0->format('m/d/y');
        $actualTimestamp = $frmtr->parse($dateStr);

        $d1 = $this->adjustDateTimeFromNow($interval1);
        $expectedTimestamp = $d1->getTimestamp();

        $this->assertEquals($expectedTimestamp, $actualTimestamp);
        
    }
    
    function intervalProvider() {

	    $testArray = array(
            '+21 = -79' => ['+21', '-79'],
	        '+20 = +20' => ['+20', '+20'],
            '+19 = +19' => ['+19', '+19'],
	        '-79 = -79' => ['-79', '-79'],
            '-80 = -80' => ['-80', '+20'],
            '-81 = +19' => ['-81', '+19'],
        );

	    return $testArray;
    }


    function adjustDateTimeFromNow($intervalSpec) : \DateTime {

	    // new date time object
	    $dt = new \DateTime();

	    // the format string used by the IntlDateFormatter has no time component to it, so remove the time here as well so the
        // timestamps line up.
	    $dt->setTime(0,0,0);

        $sign = substr($intervalSpec, 0, 1);
        // demonstrate the use of the invert property on a DateInterval object.  Could just as easily use add / sub methods on the date object
        $invert = ('+' == $sign ? 0 : 1);
        $intervalString = 'P' . substr($intervalSpec, 1, strlen($intervalSpec)) . 'Y';
        $interval = new \DateInterval($intervalString);
        $interval->invert = $invert;
        $dt->add($interval);
        return $dt;
    }
	
    
    function testLocalesTimezones() {

	    $dateStrUS = '6/7/2020';
        $frmtrUS = new \IntlDateFormatter('en-US', IntlDateFormatter::SHORT, IntlDateFormatter::NONE, 'America/New_York', IntlDateFormatter::GREGORIAN);
        $timestampUS = $frmtrUS->parse($dateStrUS);

        // dates formatted d/m/y in Europe
        $dateStrFR = '7/6/2020';
        $frmtrFR = new \IntlDateFormatter('fr-FR', IntlDateFormatter::SHORT, IntlDateFormatter::NONE, 'Europe/Paris', IntlDateFormatter::GREGORIAN);
        $timestampFR = $frmtrFR->parse($dateStrFR);

        $intlFrmtrOffset = $timestampFR - $timestampUS;

        // pain in the rear but let's check this against the standard PHP DateTime machinery
        $nytz = new \DateTimeZone('America/New_York');
        $dateUS = new \DateTime("now", $nytz);
        $dateUS->setTimestamp($timestampUS);

        $paristz = new \DateTimeZone('Europe/Paris');
        $dateFR = new \DateTime("now", $paristz);
        $dateFR->setTimestamp($timestampFR);
        $dateTimeOffset = $dateUS->getOffset() - $dateFR->getOffset();

        self::assertEquals($intlFrmtrOffset, $dateTimeOffset);

    }

    function testSeparators() {

        $frmtrUS = new \IntlDateFormatter('en-US', IntlDateFormatter::SHORT, IntlDateFormatter::NONE, 'America/New_York', IntlDateFormatter::GREGORIAN);
        $dateStr = '5/13/2020';
        $frmtrUS->setPattern('m/d/Y');
        $date1 = $frmtrUS->parse($dateStr);

        $dateStr = '5-13-2020';
        $frmtrUS->setPattern('m-d-Y');
        $date2 = $frmtrUS->parse($dateStr);

        $dateStr = '5.13.2020';
        $frmtrUS->setPattern('m.d.Y');
        $date3 = $frmtrUS->parse($dateStr);

        self::assertEquals($date1, $date2);
        self::assertEquals($date1, $date3);

    }

    function testTimeFormats() {
        $frmtr = new \IntlDateFormatter('en-US', IntlDateFormatter::NONE, IntlDateFormatter::SHORT, 'America/New_York', IntlDateFormatter::GREGORIAN);

        // default format from constructor
        $expectedResult = 'h:mm a';
        $this->assertEquals($expectedResult, $frmtr->getPattern());

        $frmtr = new \IntlDateFormatter('de_DE', IntlDateFormatter::NONE, IntlDateFormatter::SHORT, 'Europe/Berlin', IntlDateFormatter::GREGORIAN);
        $expectedResult = 'HH:mm';
        $this->assertEquals($expectedResult, $frmtr->getPattern());

    }
}

?>