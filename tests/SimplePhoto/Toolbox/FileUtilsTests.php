<?php

namespace SimplePhoto\Toolbox;

function finfo_open($options)
{
    $finfo = array();
    if ($options & FILEINFO_MIME_TYPE) {
        $finfo['mode'] = 'mime';
    }

    return $finfo;
}

function finfo_file($finfo, $file)
{
    if (array_key_exists('mode', $finfo)) {
        if ($finfo['mode'] == 'mime') {
            return 'image/png';
        }
    }

    return null;
}

function copy($source, $dest)
{
    if ($source == 'mock/file.png') {
        return true;
    }

    return \copy($source, $dest);
}

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

    public function testNormalizePath()
    {
        $this->assertEquals('some/path', FileUtils::normalizePath('/some/./path/'));
        $this->assertEquals('some/path', FileUtils::normalizePath('/some/subfolder/../path/'));
    }

    public function testCreateTmpFile()
    {
        $file = FileUtils::createTempFile('mock/file.png');
        $this->assertFileExists($file);
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
        $this->assertEquals('image/png', FileUtils::getMime('/usr/bin/file.png'));
    }

    public function testGetMimeFromFileExtension()
    {
        $this->assertEquals('image/png', FileUtils::getMimeFromExtension('png'));
        $this->assertEquals('image/jpeg', FileUtils::getMimeFromExtension('jpeg'));
        $this->assertEquals('image/jpeg', FileUtils::getMimeFromExtension('jpg'));
        $this->assertNull(FileUtils::getMimeFromExtension('exe'));
    }

    public function testGetFileExtension()
    {
        $this->assertEquals('png', FileUtils::getExtension('/files/photos/32513g41k3j4g23j41.png'));
    }

    public function testGetFileExtensionFromMime()
    {
        $this->assertEquals('png', FileUtils::getExtensionFromMime('image/png'));
        $this->assertEquals('jpg', FileUtils::getExtensionFromMime('image/jpeg'));
        $this->assertEquals('jpg', FileUtils::getExtensionFromMime('image/jpg'));
        $this->assertNull(FileUtils::getExtensionFromMime('application/octet-stream'));
    }
}
