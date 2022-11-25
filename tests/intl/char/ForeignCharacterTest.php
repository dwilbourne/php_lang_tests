<?php
/**
 * @package: pvc
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version: 1.0
 */

namespace tests\intl\char;

use PHPUnit\Framework\TestCase;

class ForeignCharacterTest extends TestCase {

    function testFrenchCharacter() {
        $e_with_acute = "\u{00E9}";
        $snowman = "\u{2603}";

        $this->assertFalse(ctype_alpha($e_with_acute));

        setlocale(LC_CTYPE, 'fr-FR');

        // ctype_alpha is NOT locale aware
        $this->assertFalse(ctype_alpha($e_with_acute));

        setlocale(LC_CTYPE, 'en-US');

        // \IntlChar::isalpha is not locale aware either but handles Unicode characters
        $this->assertFalse(\IntlChar::isalpha($snowman));

        $this->assertEquals(\IntlChar::CHAR_CATEGORY_LOWERCASE_LETTER, \IntlChar::charType($e_with_acute));

        $this->assertTrue(\IntlChar::isalpha($e_with_acute));

    }

}
