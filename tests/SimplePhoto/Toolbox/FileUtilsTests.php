<?php

namespace SimplePhoto\Toolbox;

/**
 * @author Laju Morrison <morrelinko@gmail.com>
 */
class FileUtilsTests extends \PHPUnit_Framework_TestCase
{
    public function testCheckIfaPathIsAbsolute()
    {
        $this->assertTrue(FileUtils::isAbsolute('C://php/bin'));
        $this->assertTrue(FileUtils::isAbsolute('/usr/local/php/bin'));
        $this->assertFalse(FileUtils::isAbsolute('../php/bin'));
        $this->assertFalse(FileUtils::isAbsolute('bin'));
    }

    public function testGetMimeTypeFromContentBuffer()
    {
        $this->assertEquals('text/plain', FileUtils::getContentMime('Hello PHP!'));
    }

    public function testGetSizeFromContentBuffer()
    {
        $this->assertEquals(23, FileUtils::getContentSize('some file contains this'));
    }

    public function testGetFileMime()
    {

    }

    public function testGetMimeFromFileExtension()
    {

    }

    public function testGetFileExtension()
    {

    }

    public function testGetFileExtensionFromMime()
    {

    }
}
