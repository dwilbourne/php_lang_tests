<?php
/**
 * @package: pvc
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version: 1.0
 */

namespace tests;

use PHPUnit\Framework\TestCase;


/**
 * Class CdnTest
 */
class CdnTest  extends TestCase {

    function testGetExtensionOfCdnResource() {
        $cdn = "https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js";
        $extActual = pathinfo($cdn, PATHINFO_EXTENSION);
        $extExpected = "js";
        $this->assertSame($extExpected, $extActual);
    }


}