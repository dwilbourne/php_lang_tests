<?php
/**
 * @package: pvc
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version: 1.0
 */

namespace tests\regex;

use PHPUnit\Framework\TestCase;

class PregReplaceTest extends TestCase {

    function testPregReplaceWithArrays() {

        $pattern = array("/'\+'/", "/'-'/");
        $replacement = array("+", "-");
        $subject = "#,##0.###'+';'-'#,##0.###";

        $expectedResult = "#,##0.###+;-#,##0.###";
        $actualResult = preg_replace($pattern, $replacement, $subject);
        self::assertEquals($expectedResult, $actualResult);

    }

    function testPregreplaceWithBackReferences() {

        $pattern = "/'([+-])'/";
        $replacement = '$1';
        $subject = "#,##0.###'+';'-'#,##0.###";

        $expectedResult = "#,##0.###+;-#,##0.###";
        $actualResult = preg_replace($pattern, $replacement, $subject);
        self::assertEquals($expectedResult, $actualResult);

    }

    function testPregReplaceACerainCharacters()
    {
        $pattern = '/a-zA-Z/';
        $replacement = '_';
        $subject = '#ABC#12';
        $expectedResult = '_ABC_12';
        self::assertEquals($expectedResult, preg_replace($pattern, $replacement, $subject));
    }

}
