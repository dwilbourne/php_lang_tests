<?php
/**
 * @package: pvc
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version: 1.0
 */

namespace tests\html;

use PHPUnit\Framework\TestCase;

/**
 * Class HtmlSpecialChars
 *
 *
 * Why use html_special_chars instead of html_entities?
 *
 * Because (from Stackoverflow) :
 *
 * Sometimes you're writing XML data, and you can't use HTML entities in a XML file.
 *
 * Because htmlentities substitutes more characters than htmlspecialchars. This is unnecessary, makes the PHP
 * script less efficient and the resulting HTML code less readable. htmlentities is only necessary if your
 * pages use encodings such as ASCII or LATIN-1 instead of UTF-8 and you're handling data with an encoding
 *  different from the page's.
 *
 * echo htmlentities('<Il était une fois un être>.');
 * // Output: &lt;Il &eacute;tait une fois un &ecirc;tre&gt;.
 * //                ^^^^^^^^                 ^^^^^^^
 * echo htmlspecialchars('<Il était une fois un être>.');
 * // Output: &lt;Il était une fois un être&gt;.
 * //                ^
 *
 * In a nutshell: sanitizing output for a browser is different than for XML and other environments.  The key
 * is to know what program is going to be decoding your response and use the proper sanitation tool.
 */

class HtmlSpecialChars extends TestCase {

    function testDisplayHtmlEntitiesTranslationTable() {
        $result = implode( "\t", array_values( get_html_translation_table( HTML_ENTITIES )));
        echo $result . PHP_EOL;
        self::assertTrue(true);
    }

    function testDisplayHtmlSpecialCharsTranslationTable() {
        $result = implode( "\t", array_values( get_html_translation_table( HTML_SPECIALCHARS )));
        echo $result . PHP_EOL;
        self::assertTrue(true);
    }

}