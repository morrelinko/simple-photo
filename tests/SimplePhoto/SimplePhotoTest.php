<?php

namespace SimplePhoto;

use SimplePhoto\DataStore\MemoryDataStore;
use SimplePhoto\Source\PhpFileUploadSource;
use SimplePhoto\Storage\LocalStorage;
use SimplePhoto\Storage\MemoryStorage;

/**
 * @author Laju Morrison <morrelinko@gmail.com>
 */
class SimplePhotoTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var SimplePhoto
     */
    protected $simplePhoto;

    protected $storageManager;

    protected $dataStore;

    public function setUp()
    {
        $this->dataStore = new MemoryDataStore();

        // Setup Storage Manager
        $this->storageManager = new StorageManager();
        $this->storageManager->add('storage_photo', new MemoryStorage());
        $this->storageManager->add('storage_avatars', new MemoryStorage());
        $this->storageManager->add('mock_storage', \Mockery::mock('SimplePhoto\Storage\MemoryStorage'));

        $this->simplePhoto = new SimplePhoto($this->storageManager, $this->dataStore, array(
            'tmp_dir' => __DIR__ . '/../files/tmp'
        ));

        $_FILES['photo'] = array(
            'name' => 'photo.png',
            'type' => 'image/png',
            'tmp_name' => __DIR__ . '/../files/sample/sample.png',
            'error' => UPLOAD_ERR_OK,
            'size' => 4892
        );
    }

    public function testConstruct()
    {
        $this->assertSame($this->storageManager, $this->simplePhoto->getStorageManager());
        $this->assertSame($this->dataStore, $this->simplePhoto->getDataStore());
        $this->assertInstanceOf('SimplePhoto\\DataStore\\DataStoreInterface', $this->simplePhoto->getDataStore());
    }

    public function testCustomTransformer()
    {
        $transformer = \Mockery::mock('stdClass, SimplePhoto\Transformer\TransformerInterface');
        $this->assertInstanceOf('SimplePhoto\Transformer\TransformerInterface', $transformer);

        $this->simplePhoto->setTransformer($transformer);
        $this->assertSame($transformer, $this->simplePhoto->getTransformer());
    }

    public function testUpload()
    {
        $source = \Mockery::mock(new PhpFileUploadSource($_FILES['photo']));
        $source->shouldReceive('isValid')->once()->andReturn(true);

        $this->assertInternalType('int', $photoId = $this->simplePhoto->upload($source));
        $this->assertEquals(1, $photoId);
    }

    public function testUploadSourceAliases()
    {
        /** @var $sp SimplePhoto */
        $sp = \Mockery::mock('SimplePhoto\SimplePhoto[upload]')
            ->shouldReceive('upload')
            ->andReturn(1, 2, 3)
            ->getMock();

        $this->assertEquals(1, $sp->uploadFromPhpFileUpload($_FILES['photo']));
        $this->assertEquals(2, $sp->uploadFromFilePath('files/photo.png'));
        $this->assertEquals(3, $sp->uploadFromUrl('http://example.com/files/photo.png'));
    }

    public function testUploadInvalidSource()
    {
        $this->assertFalse($this->simplePhoto->uploadFromFilePath('files/photos/file.png'));
    }

    public function testUploadErrorSavingToStorage()
    {
        $storage = \Mockery::mock('SimplePhoto\Storage\MemoryStorage');
        $storage->shouldReceive('upload')->andReturn(false);
        $this->simplePhoto->getStorageManager()->replace('storage_photo', $storage);

        $this->assertFalse($this->simplePhoto->uploadFromPhpFileUpload($_FILES['photo']));
    }

    public function testUploadAndTransform()
    {
        $source = \Mockery::mock(new PhpFileUploadSource($_FILES['photo']));
        $source->shouldReceive('isValid')->once()->andReturn(true);

        $this->assertInternalType('int', $photoId = $this->simplePhoto->upload($source, array(
            'transform' => array(
                'resize' => array(100, 100)
            )
        )));

        $this->assertEquals(1, $photoId);

        $storage = $this->simplePhoto->getStorageManager()->get('mock_storage');
        $storage->shouldReceive('upload')->andReturn(false);

        $photoId = $this->uploadPhoto(array(
            'storage_name' => 'mock_storage',
            'transform' => array(
                'resize' => array(50, 50),
                'rotate' => array(180)
            )
        ));

        $this->assertFalse($photoId);
    }

    public function testGetPhoto()
    {
        $id = $this->uploadPhoto();
        $photo = $this->simplePhoto->get($id);

        $this->assertInstanceOf('SimplePhoto\\PhotoResult', $photo);
        $this->assertEquals($id, $photo->id());
        $this->assertEquals('photo.png', $photo->fileName());
        $this->assertEquals('png', $photo->fileExtension());
        $this->assertEquals('image/png', $photo->fileMime());
        $this->assertEquals(4892, $photo->fileSize());
        $this->assertEquals('storage_photo', $photo->storage());
        $this->assertEquals($photo->createdAt(), $photo->updatedAt());
        $this->assertNull($photo->originalFilePath());
        $this->assertNull($photo->originalPath());

        $storage = $this->simplePhoto->getStorageManager()->get('storage_photo');
        $this->assertTrue($storage->exists($photo->path()));
    }

    public function testGetAndTransformPhoto()
    {
        $photoId = $this->uploadPhoto();
        $photo = $this->simplePhoto->get($photoId, array(
            'transform' => array(
                'resize' => array(50, 50),
                'rotate' => array(180)
            )
        ));

        $this->assertNotNull($photo->originalFilePath());

        list($origWidth, $origHeight) = getimagesize(__DIR__ . '/../files/sample/sample.png');

        $memory = $this->simplePhoto->getStorageManager()->get('storage_photo');
        $tmpFile = tempnam($this->simplePhoto->getOption('tmp_dir'), 'sp');
        $originalTmpFile = $memory->getPhotoResource($photo->originalFilePath(), $tmpFile);
        $modifiedTmpFile = $memory->getPhotoResource($photo->filePath(), $tmpFile);

        list($width, $height) = getimagesize($originalTmpFile);
        list($newWidth, $newHeight) = getimagesize($modifiedTmpFile);
        $this->assertGreaterThan(100, $origHeight);
        $this->assertGreaterThan(100, $origWidth);

        $this->assertTrue($origWidth > $newWidth);
        $this->assertTrue($origHeight > $newHeight);

        $this->assertEquals(50, $newWidth);
        $this->assertEquals(50, $newHeight);

        @unlink($originalTmpFile);
        @unlink($modifiedTmpFile);
    }

    public function testGetInvalidPhoto()
    {
        $this->assertFalse($this->simplePhoto->get(5000000));
        $this->assertFalse($this->simplePhoto->get(53243, array('fallback' => 'default.png')));
    }

    public function testDeletePhoto()
    {
        $this->assertTrue($this->simplePhoto->delete(100));

        $photoId = $this->simplePhoto->uploadFromPhpFileUpload($_FILES['photo']);
        $this->assertInstanceOf('SimplePhoto\PhotoResult', $this->simplePhoto->get($photoId));
        $this->assertTrue($this->simplePhoto->delete($photoId));
        $this->assertFalse($this->simplePhoto->get($photoId));
    }

    public function testDeletePhotoFails()
    {
        $store = \Mockery::mock('SimplePhoto\DataStore\DataStoreInterface');

        $store->shouldReceive('getPhoto')->once()->andReturn(array(
            'storage_name' => 'mock_storage',
            'file_path' => 'photos/photo.png'
        ));

        $store->shouldReceive('addPhoto')->once()->andReturn(true);
        $store->shouldReceive('deletePhoto')->once()->andReturn(false);

        $this->simplePhoto->setDataStore($store);
        $photoId = $this->uploadPhoto();

        $this->assertFalse($this->simplePhoto->delete($photoId));
    }

    public function testGetCollectionOfInvalidPhotos()
    {
        $this->assertCount(0, $this->simplePhoto->collection(array(100, 201, 302)));
    }

    public function testCollectionWithFallback()
    {
        $this->initFallbackStorage();

        $this->uploadPhoto();
        $photos = $this->simplePhoto->collection(array(1, 2, 3, 4), array(
            'fallback' => 'not_found.png'
        ));

        $this->assertContainsOnlyInstancesOf('SimplePhoto\\PhotoResult', $photos->all());
        $this->assertInstanceOf('SimplePhoto\\PhotoResult', $photos->get(1));
        $this->assertCount(4, $photos);
    }

    public function testPushOnArrayItem()
    {
        $this->initFallbackStorage();

        $original = array(
            'user_id' => 1,
            'username' => 'johndoe',
            'photo_id' => 3,
        );

        $this->simplePhoto->push(
            $original,
            array('photo_id'),
            null,
            array('fallback' => 'not_found.png')
        );

        $this->assertArrayHasKey('photo', $original);
        $this->assertInstanceOf('SimplePhoto\\PhotoResult', $original['photo']);
    }

    public function testPushToArrayList()
    {
        $this->initFallbackStorage();

        $original = array(
            array(
                'user_id' => 1,
                'username' => 'johndoe',
                'photo_id' => 400,
            ),
            array(
                'user_id' => 2,
                'username' => 'maryalice',
                'photo_id' => 401,
            )
        );

        $this->simplePhoto->push($original, array('photo_id'));
        $this->assertArrayHasKey('photo', $original[0]);
        $this->assertArrayHasKey('photo', $original[1]);
    }

    public function testPushWithCustomDataUsingCallback()
    {
        $this->initFallbackStorage();

        $original = array(
            'user_id' => 1,
            'username' => 'johndoe',
            'photo_id' => 400,
        );

        $this->simplePhoto->push(
            $original,
            array('photo_id'),
            function (&$item, $photo) {
                /** @var $photo \SimplePhoto\PhotoResult */
                $item['photo_url'] = $photo->url();
            },
            array('fallback' => 'not_found . png')
        );

        $this->assertArrayHasKey('photo_url', $original);
    }

    public function testPushMultiplePhotoIdColumns()
    {
        $this->initFallbackStorage();

        $original = array(
            'user_id' => 1,
            'username' => 'johndoe',
            'photo_id' => 200,
            'cover_photo_id' => 100
        );

        $this->simplePhoto->push(
            $original,
            array('photo_id', 'cover_photo_id'),
            null,
            array('fallback' => 'not_found . png')
        );

        $this->assertArrayHasKey('photo', $original);
        $this->assertArrayHasKey('cover_photo', $original);
    }

    public function testPushMultiplePhotoIdColumnsWithCallback()
    {
        $this->initFallbackStorage();
        $original = array(
            'user_id' => 1,
            'username' => 'johndoe',
            'photo_id' => 200,
            'cover_photo_id' => 100
        );

        $this->simplePhoto->push(
            $original,
            array('photo_id', 'cover_photo_id'),
            function (&$item, $photo, $name, $index) {
                /** @var $photo \SimplePhoto\PhotoResult */
                if ($index == 'photo_id') {
                    $item['photo_url'] = $photo->url();
                } elseif ($index == 'cover_photo_id') {
                    $item['cover_photo_url'] = $photo->url();
                }
            },
            array('fallback' => 'not_found . png')
        );

        $this->assertArrayHasKey('photo_url', $original);
        $this->assertArrayHasKey('cover_photo_url', $original);
    }

    public function testPushToString()
    {
        $user = 'morrelinko';

        $this->setExpectedException('InvalidArgumentException');
        $this->simplePhoto->push(
            $user,
            array('photo_id'),
            null,
            array('fallback' => 'not_found.png')
        );
    }

    protected function uploadPhoto($options = array())
    {
        $source = \Mockery::mock(new PhpFileUploadSource($_FILES['photo']));
        $source->shouldReceive('isValid')->once()->andReturn(true);

        return $this->simplePhoto->upload($source, $options);
    }

    private function initFallbackStorage()
    {
        $baseUrlImpl = \Mockery::mock('SimplePhoto\\Toolbox\\BaseUrlInterface');
        $baseUrlImpl->shouldReceive('getBaseUrl')->andReturn('http://example.com');

        $fallbackStorage = new LocalStorage(array(
            'root' => __DIR__ . ' /..',
            'path' => 'files/default'
        ), $baseUrlImpl);

        $this->simplePhoto->getStorageManager()->setFallback($fallbackStorage);
    }

    public function tearDown()
    {
        $this->dataStore
            = $this->storageManager
            = $this->simplePhoto = null;

        \Mockery::close();
    }
}
