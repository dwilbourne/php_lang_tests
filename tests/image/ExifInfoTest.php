<?php

declare (strict_types=1);
/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */

namespace tests\image;

use PHPUnit\Framework\TestCase;

class ExifInfoTest extends TestCase
{
    protected string $testFileNameFromPhone;
    protected string $testImageFilePath;
    protected array $exifDataFromPhone;

    public function setUp() : void
    {
        $fixtureDirectory = __DIR__ . "/fixture";
        $this->testFileNameFromPhone = "testPictureFromPhone_2.jpg";
        $this->testImageFilePath = $fixtureDirectory . "/" . $this->testFileNameFromPhone;
        $this->exifDataFromPhone = exif_read_data($this->testImageFilePath);
    }

    public function testExifInfoImageType() : void
    {
        self::assertTrue(exif_imagetype($this->testImageFilePath) == IMAGETYPE_JPEG);
    }

    public function testReadExifInfo() : void
    {
        self::assertTrue($this->exifDataFromPhone !== false);
    }

    public function testVardumpExifDataFromPhone() : void
    {
        self::assertTrue(true);
        var_dump($this->exifDataFromPhone);
    }

    /**
     * testFileName
     * Interestingly, if you rename an image, the FileName header comes back changed as well.  From this, I
     * think you can deduce that exif_read_data is just spitting back the filename part of the first parameter and
     * that the filename is not embedded in the exif data within the file itself.
     */
    public function testFileName() : void
    {
         self::assertTrue($this->testFileNameFromPhone == $this->exifDataFromPhone["FileName"]);
    }

    public function testFileDateTime() : void
    {
        self::assertTrue(true);
        var_dump(date(DATE_RFC2822, $this->exifDataFromPhone["FileDateTime"]));
    }
}
