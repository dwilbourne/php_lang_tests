<?php

declare (strict_types=1);
/**
 * @package {PROJECT_NAME}
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */

namespace tests\file;

use PHPUnit\Framework\TestCase;

class FileGetContentsTest extends TestCase
{

    public function testFileGetContentsOnNonExistentFile(): void
    {
        /**
         * issues a warning if file does not exist
         */
        $this->expectWarning();
        $fileContents = file_get_contents("foo");
    }

}
