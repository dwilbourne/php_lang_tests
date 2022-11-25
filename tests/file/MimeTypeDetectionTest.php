<?php
/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version 1.0
 */

namespace tests;

use PHPUnit\Framework\TestCase;

class MimeTypeDetectionTest extends TestCase
{
    protected $fixturesDir = __DIR__ . '/fixtures/';

    /**
     * testMimeTypeDetection
     * @param $expectedResult
     * @param $filename
     * @dataProvider dataProvider
     */
    public function testMimeTypeDetection($expectedResult, $filename) {
        $filePath = $this->fixturesDir . $filename;
        self::assertEquals($expectedResult, mime_content_type($filePath));
    }

    public function dataProvider() : array {
        return [
            ['text/html', 'document-invalid-html4.html'],
            ['text/html', 'document-valid-ISO-html5.html'],
            ['text/html', 'document-valid-UTF8-html5.html'],
            ['text/xml', 'xml-invalid-basic.xml']
            ];
    }
}
