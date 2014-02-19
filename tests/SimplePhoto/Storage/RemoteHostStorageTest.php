<?php

namespace SimplePhoto\Storage;

/**
 * @author Laju Morrison <morrelinko@gmail.com>
 */
class RemoteHostStorageTest extends \PHPUnit_Framework_TestCase
{
    const SAMPLE_FILE_NAME = 'sample_file';

    /**
     * @var RemoteHostStorage
     */
    protected $storage;

    protected $options = array(
        'path' => 'avatars',
        'host' => '127.0.0.1',
        'root' => '/',
        'url' => 'http://static.img-ex.com',
        'username' => 'user',
        'password' => 'xyz',
        'port' => 21
    );

    public function setUp()
    {
        require_once 'ftp_functions.php';

        $this->storage = new RemoteHostStorage($this->options);
    }

    public function testInitialize()
    {
        $this->assertEquals('/', $this->storage->getRoot());
        $this->assertEquals('127.0.0.1', $this->storage->getHost());
        $this->assertEquals('http://static.img-ex.com', $this->storage->getUrl());
        $this->assertEquals(21, $this->storage->getPort());
        $this->assertEquals('/', $this->storage->getRoot());
        $this->assertEquals('user', $this->storage->getUsername());
        $this->assertEquals('xyz', $this->storage->getPassword());
        $this->assertEquals('avatars', $this->storage->getPath());
    }

    public function testConnection()
    {
        $this->assertFalse($this->storage->isConnected());
        $this->storage->connection();
        $this->assertTrue($this->storage->isConnected());
    }

    public function testConnectionWithWrongHost()
    {
        // Create a storage with wrong host
        $storage = new RemoteHostStorage(array_merge(
            $this->options,
            array(
                'path' => 'avatars',
                'host' => '189.156.6.1'
            )
        ));

        $this->setExpectedException('RuntimeException');
        $storage->connect();
    }

    public function testConnectionWithWrongCredentials()
    {
        // Create a storage with wrong password
        $storage = new RemoteHostStorage(
            array_merge($this->options, array(
                'password' => 'pa$$word'
            ))
        );

        $this->setExpectedException('RuntimeException');
        $storage->connect();
    }

    public function testUploadFile()
    {
        $file = tempnam(sys_get_temp_dir(), null);
        $this->storage->upload($file, self::SAMPLE_FILE_NAME);
        unlink($file);
    }

    public function testDeletePhoto()
    {
        $this->assertTrue($this->storage->deletePhoto('file.png'));
        $this->assertTrue($this->storage->deletePhoto('file_that_does_not_exists.png'));
        $this->assertFalse($this->storage->deletePhoto('fails.png'));
    }

    public function testUploadFileFail()
    {
        $file = tempnam(sys_get_temp_dir(), null);
        $result = $this->storage->upload($file, 'this/upload/fails.png');
        $this->assertFalse($result);
        unlink($file);
    }

    public function testUploadInvalidFile()
    {
        $file = 'path/to/unknown/file.png';
        $this->setExpectedException('RuntimeException');
        $this->storage->upload($file, 'some_dir/');
    }

    public function testGetPhotoResource()
    {
        $tmpName = $this->storage->getPhotoResource('files/photo.png');
        $this->assertFileExists($tmpName);
        $this->assertSame('contents', file_get_contents($tmpName));
        unlink($tmpName);
    }

    public function testGetPhotoPath()
    {
        $this->assertEquals(
            '/avatars/files/photo.png',
            $this->storage->getPhotoPath('files/photo.png')
        );
    }

    public function testGetPhotoUrl()
    {
        $this->assertEquals(
            'http://static.img-ex.com/avatars/files/photo.png',
            $this->storage->getPhotoUrl('files/photo.png')
        );
    }

    public function testVerifyPathExists()
    {
        $this->assertEquals(
            'public_html',
            $this->storage->verifyPathExists('public_html')
        );

        $this->setExpectedException('RuntimeException');
        $this->storage->verifyPathExists('path/not/found/fails.png', false);
    }

    public function testCreateDirectory()
    {
        $this->assertTrue($this->storage->directoryExists('/'));
        $this->assertTrue($this->storage->createDirectory('parent/sub/name'));

        $this->setExpectedException('RuntimeException');
        $this->storage->createDirectory('create/dir/fails');
    }
}
