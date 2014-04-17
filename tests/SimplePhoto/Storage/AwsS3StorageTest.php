<?php

namespace SimplePhoto\Storage;

use Mockery;

/**
 * @author Laju Morrison <morrelinko@gmail.com>
 */
class AwsS3StorageTest extends \PHPUnit_Framework_TestCase
{
    public function testUpload()
    {
        $client = $this->getClient();
        $client->shouldReceive('putObject')->once()->andThrow('RuntimeException');
        $client->shouldReceive('putObject')->once()->andReturn(true);
        $storage = $this->createStorage($client);

        $this->assertFalse($storage->upload('/some/file.png', 'photo.png'));
        $this->assertEquals('photo.png', $storage->upload('/some/file.png', 'photo.png'));
    }

    public function testGetInfo()
    {
        $client = $this->getClient();
        $client->shouldReceive('headObject')->once()->andReturn(array(
            'ContentLength' => 442323
        ));

        $storage = $this->createStorage($client);

        $info = $storage->getInfo('photo.png');
        $this->assertArrayHasKey('file_size', $info);
        $this->assertEquals(442323, $info['file_size']);
    }

    public function testDeletePhoto()
    {
        $client = $this->getClient();
        $client->shouldReceive('deleteObject')->zeroOrMoreTimes()->andReturn(true);
        $storage = $this->createStorage($client);

        $this->assertTrue($storage->deletePhoto('photo.png'));
    }

    public function testGetPhotoPath()
    {
        $client = $this->getClient();
        $storage = $this->createStorage($client);

        $this->assertEquals('photos/photo.png', $storage->getPhotoPath('photo.png'));
    }

    public function testGetObjectUrl()
    {
        $client = $this->getClient();
        $client->shouldReceive('getObjectUrl')->once()->andReturn('http://s3-photos.amazon.com/photos/photo.png');
        $storage = $this->createStorage($client);

        $this->assertEquals('http://s3-photos.amazon.com/photos/photo.png', $storage->getPhotoUrl('photo.png'));
    }

    public function testGetPhotoResource()
    {
        $client = $this->getClient();
        $client->shouldReceive('getObject')->once()->andReturn(true);
        $storage = $this->createStorage($client);

        $this->assertFileExists($storage->getPhotoResource('photo.png'));
    }

    public function testExists()
    {
        $client = $this->getClient();
        $client->shouldReceive('doesObjectExist')->once()->andReturn(true);
        $storage = $this->createStorage($client);

        $this->assertTrue($storage->exists('photo.png'));
    }

    protected function createStorage($client, $options = array())
    {
        $options = array_merge(array(
            'bucket' => 's3-photo',
            'directory' => 'photos',
            'acl' => 'public-read'
        ), $options);

        return new AwsS3Storage($client, $options);
    }

    protected function getClient()
    {
        return Mockery::mock('Aws\S3\S3Client');
    }

    public function tearDown()
    {
        \Mockery::close();
    }
}
