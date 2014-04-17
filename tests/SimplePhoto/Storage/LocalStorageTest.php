<?php

namespace SimplePhoto\Storage;

use SimplePhoto\Toolbox\FileUtils;
use SimplePhoto\Toolbox\TextUtils;

function unlink($file)
{
    if (TextUtils::endsWith($file, 'test/photo.png')) {
        return true;
    }

    if (TextUtils::endsWith($file, 'test/unlink.fail.png')) {
        return false;
    }

    return \unlink($file);
}

function is_file($file)
{
    if (TextUtils::endsWith($file, 'test/invalid.png')) {
        return false;
    }

    if (TextUtils::endsWith($file, 'test/photo.png')
        || TextUtils::endsWith($file, 'test/fails.png')
        || TextUtils::endsWith($file, 'test/unlink.fail.png')
    ) {
        return true;
    }

    return \is_file($file);
}

function is_dir($dir)
{
    if (TextUtils::endsWith($dir, 'test/dir/invalid')
        || TextUtils::endsWith($dir, 'test/dir/mkdirfails')
    ) {
        return false;
    }

    if (TextUtils::endsWith($dir, 'test/dir/photos')) {
        return true;
    }

    return \is_dir($dir);
}

function file_exists($file)
{
    if (TextUtils::endsWith($file, 'test/invalid.png')) {
        return false;
    }

    if (TextUtils::endsWith($file, 'test/photo.png')
        || TextUtils::endsWith($file, 'test/fails.png')
        || TextUtils::endsWith($file, 'test/unlink.fail.png')
    ) {
        return true;
    }

    return \file_exists($file);
}

function filesize($file)
{
    if (TextUtils::endsWith($file, 'test/photo.png')) {
        return 1234;
    }

    return \filesize($file);
}

function copy($file, $target)
{
    if (TextUtils::endsWith($file, 'test/photo.png')) {
        return true;
    }

    if (TextUtils::endsWith($file, 'test/fails.png')) {
        return false;
    }

    if ($file == 'tmp/temp1234.tmp') {
        return true;
    }

    return \copy($file, $target);
}

function mkdir($dir, $mode = 0777, $recursive = true)
{
    if (TextUtils::endsWith($dir, 'test/dir/mkdirfails')) {
        return false;
    }

    if (TextUtils::endsWith($dir, 'test/dir/mkdirpass')) {
        return true;
    }

    return \mkdir($dir, $mode, $recursive);
}

function tempnam($dir, $prefix = null)
{
    return 'tmp/' . $prefix . '1234.tmp';
}

/**
 * @author Laju Morrison <morrelinko@gmail.com>
 */
class LocalStorageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var LocalStorage
     */
    protected $storage;

    protected $projectPath;

    protected $savePath;

    public function setUp()
    {
    }

    public function testGetPhotoData()
    {
        $storage = $this->createStorage();
        $photoPath = __DIR__ . '/../../files/photo/uploaded_file.txt';
        $photoPath = FileUtils::normalizePath($photoPath);

        $this->assertEquals($photoPath, $storage->getPhotoPath('uploaded_file.txt'));
    }

    public function testGetPhotoUrl()
    {
        $storage = $this->createStorage($this->createBaseUrlImpl());
        $this->assertEquals(
            'http://example.com/files/photo/uploaded_file.png',
            $storage->getPhotoUrl('uploaded_file.png')
        );

        $_SERVER['HTTP_HOST'] = 'site.com';
        $_SERVER['SERVER_PORT'] = 80;
        $_SERVER['PHP_SELF'] = '/';
        $storage = $this->createStorage();

        $this->assertEquals(
            'http://site.com/files/photo/uploaded_file.png',
            $storage->getPhotoUrl('uploaded_file.png')
        );

        $_SERVER['HTTPS'] = 'on';
        $this->assertEquals(
            'https://site.com/files/photo/uploaded_file.png',
            $storage->getPhotoUrl('uploaded_file.png')
        );

        $_SERVER['SERVER_PORT'] = 443;
        $this->assertEquals(
            'https://site.com/files/photo/uploaded_file.png',
            $storage->getPhotoUrl('uploaded_file.png')
        );
    }

    public function testUploadFile()
    {
        $storage = $this->createStorage($this->createBaseUrlImpl());
        $this->assertEquals(
            'uploaded_file.png',
            $storage->upload('test/photo.png', 'uploaded_file.png')
        );
    }

    public function testUploadInvalidFile()
    {
        $storage = $this->createStorage($this->createBaseUrlImpl());

        $this->assertFalse($storage->upload('test/fails.png', 'photo.png'));
        $this->setExpectedException('RuntimeException');
        $storage->upload('test/invalid.png', 'photo.png');
    }

    public function testGetPhotoResource()
    {
        $storage = $this->createStorage($this->createBaseUrlImpl());
        $this->assertEquals('tmp/temp1234.tmp', $storage->getPhotoResource('test/photo.png'));
    }

    public function testDeletePhoto()
    {
        $storage = $this->createStorage($this->createBaseUrlImpl());
        $this->assertTrue($storage->deletePhoto('test/invalid.png'));
        $this->assertTrue($storage->deletePhoto('test/photo.png'));
        $this->assertFalse($storage->deletePhoto('test/unlink.fail.png'));
    }

    public function testVerifyPathExists()
    {
        $storage = $this->createStorage($this->createBaseUrlImpl());
        $path = FileUtils::normalizePath(__DIR__ . '/../../files/photo');

        $this->assertEquals($path, $storage->verifyPathExists($path));

        $this->setExpectedException('RuntimeException');
        $storage->verifyPathExists('test/directory', false);
    }

    public function testGetInfo()
    {
        $storage = $this->createStorage($this->createBaseUrlImpl());
        $this->assertFalse($storage->getInfo('test/invalid.png'));

        $info = $storage->getInfo('test/photo.png');
        $this->assertInternalType('array', $info);
        $this->assertArrayHasKey('file_size', $info);
        $this->assertEquals(1234, $info['file_size']);
    }

    public function testMisc()
    {
        $storage = $this->createStorage();

        $this->assertEquals('files/photo', $storage->getSavePath());
        $this->assertFalse($storage->createDirectory('test/dir/mkdirfails'));
        $this->assertTrue($storage->createDirectory('test/dir/mkdirpass'));
    }

    /**
     * @param null $baseUrlImpl
     * @return LocalStorage
     */
    protected function createStorage($baseUrlImpl = null)
    {
        return \Mockery::mock(new LocalStorage(array(
            'root' => __DIR__ . '/../..',
            'path' => 'files/photo'
        ), $baseUrlImpl));
    }

    /**
     * @return \SimplePhoto\Toolbox\BaseUrlInterface
     */
    protected function createBaseUrlImpl()
    {
        $mockBaseUrlImpl = \Mockery::mock("SimplePhoto\\Toolbox\\BaseUrlInterface");
        $mockBaseUrlImpl->shouldReceive("getBaseUrl")->andReturn('http://example.com');

        return $mockBaseUrlImpl;
    }

    public function tearDown()
    {
        \Mockery::close();
    }
}
