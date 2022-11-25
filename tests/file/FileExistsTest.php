<?php
/**
 * @package: pvc
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version: 1.0
 */

namespace tests\file;

use PHPUnit\Framework\TestCase;

class FileExistsTest extends TestCase {

    protected $filePath = 'D:/www/php_lang_tests/phpunit.xml';
    protected $hiddenFile = 'C:/pagefile.sys';

    public function testLocalFileExists() {

        $this->assertTrue(file_exists($this->filePath));

        // this file exists but is not visible to the user account running php - so understand that
        // the file_exists verb is dependent on the permissions granted to the php user

        $this->assertFalse(file_exists($this->hiddenFile));

        /**
         * according to the Microsoft docs, the important factors in the syntax below are the additional '/'
         * (for a total of 3) following the FILE 'protocol' and the 'percent encoding' of characters.  Here's the text:
         *
         * In order to avoid ambiguity, and for your Windows file paths to be interpreted correctly, characters that are
         * important to URI parsing that are also allowed in Windows file paths must be percent-encoded. This includes
         * ‘#’ and ‘%’. Characters that aren’t allowed in URIs but are allowed in Windows file paths should also be
         * percent-encoded. This includes ‘ ‘, ‘{‘, ‘}’, ‘`’, ‘^’ and all control characters. Note, for instance, that
         * the spaces in the example URIs above have been percent-encoded to ‘%20’. See the latest URI standard for the
         * full list of characters that are not allowed in URIs.
         *
         * Note that when parsed, this url will have a protocol but no host (hence the three slashes) meaning that the
         * host is local.  In Windows, the drive letter is considered a segment in the path of the url.
         */

        $file = 'FILE:///' . $this->filePath;
        $this->assertTrue(file_exists($file));
    }


}

