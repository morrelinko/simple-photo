<?php

namespace SimplePhoto;

use SimplePhoto\DataStore\SqliteDataStore;
use SimplePhoto\Source\FilePathSource;
use SimplePhoto\Storage\LocalStorage;
use SimplePhoto\Toolbox\FileUtils;

/**
 * @author Laju Morrison <morrelinko@gmail.com>
 */
class SimplePhotoTest extends \PHPUnit_Framework_TestCase
{
    const BASE_URL = "http://example.com";

    const CREATE_PHOTO_TABLE = '
        CREATE TABLE IF NOT EXISTS photos (
            id INTEGER PRIMARY KEY,
            storage_name TEXT NOT NULL,
            file_name TEXT NOT NULL,
            file_extension TEXT NOT NULL,
            file_size TEXT NOT NULL DEFAULT "0",
            file_path TEXT NOT NULL,
            file_mime TEXT NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );
    ';

    /**
     * @var SimplePhoto
     */
    protected $simplePhoto;

    protected $mockBaseUrlImpl;

    protected $photoSourceFile;

    public function setUp()
    {
        $this->mockBaseUrlImpl = \Mockery::mock('SimplePhoto\\Toolbox\\BaseUrlInterface');
        $this->mockBaseUrlImpl->shouldReceive('getBaseUrl')->andReturn(self::BASE_URL);
        $this->photoSourceFile = __DIR__ . '/../files/tmp/image_group.png';

        // Setup photo data store
        $dataStore = new SqliteDataStore(array(
            'database' => __DIR__ . '/../files/database/test_photos.db'
        ));

        // create test table
        $dataStore->getConnection()->exec(self::CREATE_PHOTO_TABLE);

        // Setup Storage locations to use..
        $localStorageOne = new LocalStorage(array(
            'root' => __DIR__ . '/..',
            'path' => 'files/photo/'
        ), $this->mockBaseUrlImpl);

        $localStorageTwo = new LocalStorage(array(
            'root' => __DIR__ . '/..',
            'path' => 'files/avatars/'
        ), $this->mockBaseUrlImpl);

        // Setup Storage Manager
        $storageManager = new StorageManager();
        $storageManager->add('local_storage_photo', $localStorageOne);
        $storageManager->add('local_storage_avatars', $localStorageTwo);

        //
        $this->simplePhoto = new SimplePhoto($storageManager, $dataStore);
    }

    public function tearDown()
    {
        $this->simplePhoto = null;
        \Mockery::close();

        $filesDir = __DIR__ . '/../files';
        if (file_exists($filesDir . '/database/test_photos.db')) {
            unlink($filesDir . '/database/test_photos.db');
        }

        try {
            $fileSplObjects = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($filesDir . '/photo'),
                \RecursiveIteratorIterator::CHILD_FIRST
            );

            foreach ($fileSplObjects as $fullFileName => $fileSplObject) {
                $fullFileName = FileUtils::normalizePath($fullFileName);
                /** @var $fileSplObject \SplFileInfo */
                if (in_array($fileSplObject->getFilename(), array('.', '..'))) {
                    continue;
                }

                if ($fileSplObject->isDir()) {
                    rmdir($fullFileName);
                } else {
                    unlink($fullFileName);
                }
            }
        } catch (\UnexpectedValueException $e) {
            printf("Files Directory contained a directory we can not re-curse into: " . $e->getMessage());
        }
    }

    public function testStorageManager()
    {
        $storageManager = $this->simplePhoto->getStorageManager();
        $this->assertInstanceOf('SimplePhoto\\StorageManager', $storageManager);

        $this->assertArrayHasKey('local_storage_photo', $storageManager->getAll());
        $this->assertArrayHasKey('local_storage_avatars', $storageManager->getAll());

        $this->assertSame('local_storage_photo', $storageManager->getDefault());
        $storageManager->setDefault('local_storage_avatars');
        $this->assertSame('local_storage_avatars', $storageManager->getDefault());

        $this->assertContainsOnlyInstancesOf(
            'SimplePhoto\\Storage\\StorageInterface',
            $storageManager->getAll()
        );

        $this->assertInstanceOf(
            'SimplePhoto\\Storage\\StorageInterface',
            $storageManager->get('local_storage_photo')
        );
    }

    public function testDataStore()
    {
        $dataStore = $this->simplePhoto->getDataStore();
        $this->assertInstanceOf('SimplePhoto\\DataStore\\DataStoreInterface', $dataStore);
    }

    public function testUploadPhoto()
    {
        $photoId = $this->simplePhoto->uploadFromFilePath($this->photoSourceFile);
        $this->assertTrue($photoId > 0);
    }

    public function testUploadFromPhpFileUpload()
    {
        // Mock uplaod _FILES
        $_FILES['image'] = array(
            'name' => '007.jpg',
            'type' => 'image/jpeg',
            'tmp_name' => __DIR__ . '/../files/tmp/image_group.png',
            'error' => 0,
            'size' => 38028
        );

        $photoId = $this->simplePhoto->uploadFromPhpFileUpload($_FILES['image']);
        $this->assertInternalType('int', $photoId);
    }

    public function testUploadAndTransformSize()
    {
        // Same as $this->simplePhoto->uploadFromFilePath()
        $photoId = $this->simplePhoto->upload(
            new FilePathSource($this->photoSourceFile)
        );

        $photo = $this->simplePhoto->get($photoId, array(
            'transform' => array(
                'resize' => array(50, 50)
            )
        ));

        $this->assertNotNull($photo->originalFilePath());

        list($origWidth, $origHeight) = getimagesize($this->photoSourceFile);
        list($width, $height) = getimagesize($photo->originalPath());
        list($newWidth, $newHeight) = getimagesize($photo->path());

        $this->assertGreaterThan(100, $origHeight);
        $this->assertGreaterThan(100, $origWidth);

        $this->assertTrue($origWidth > $newWidth);
        $this->assertTrue($origHeight > $newHeight);

        $this->assertEquals($origWidth, $width);
        $this->assertEquals($origHeight, $height);
    }

    public function testUploadWithTransformSize()
    {
        $transform = array(
            'resize' => array(100, 100)
        );

        $photoId = $this->simplePhoto->uploadFromFilePath(
            $this->photoSourceFile,
            array(
                'transform' => $transform
            )
        );

        $photo = $this->simplePhoto->get($photoId);
        list($width, $height) = getimagesize($photo->path());

        $this->assertEquals(100, $width);
        $this->assertEquals(100, $height);
    }

    public function testGetPhoto()
    {
        $date = date('Y-m-d H:i:s');
        $photoId = $this->simplePhoto->uploadFromFilePath($this->photoSourceFile);

        $photo = $this->simplePhoto->get($photoId);
        $this->assertInstanceOf('SimplePhoto\\PhotoResult', $photo);

        $this->assertSame($photo->id(), $photoId);
        $this->assertSame($photo->fileName(), 'image_group.png');
        $this->assertSame($photo->fileExtension(), 'png');
        $this->assertSame($photo->mime(), 'image/png');
        $this->assertSame($photo->createdAt(), $date);
        $this->assertSame($photo->updatedAt(), $date);
        $this->assertFileExists($photo->path());
    }

    public function testGetInvalidPhoto()
    {
        $photo = $this->simplePhoto->get(5000000);
        $this->assertFalse($photo);
    }

    public function testGetInvalidPhotoWithFallback()
    {
        $this->initFallbackStorage();
        $photo = $this->simplePhoto->get(5000000, array('fallback' => 'not_found . png'));
        $this->assertNotSame(false, $photo);
    }

    public function testDeletePhoto()
    {
        $this->assertTrue($this->simplePhoto->delete(100));

        $photoId = $this->simplePhoto->uploadFromFilePath($this->photoSourceFile);
        $this->assertInstanceOf('SimplePhoto\PhotoResult', $this->simplePhoto->get($photoId));
        $this->assertTrue($this->simplePhoto->delete($photoId));
        $this->assertSame(false, $this->simplePhoto->get($photoId));
    }

    public function testEmptyCollection()
    {
        // This behaviour is expected as
        // - these photos do not exists.
        // - no fallback storage has been defined.
        $this->assertCount(0, $this->simplePhoto->collection(array(100, 201, 302)));
    }

    public function testCollectionWithFallback()
    {
        $this->initFallbackStorage();
        $photos = $this->simplePhoto->collection(array(1, 2, 3, 4), array('fallback' => 'not_found . png'));
        $this->assertContainsOnlyInstancesOf('SimplePhoto\\PhotoResult', $photos->all());
        $this->assertInstanceOf('SimplePhoto\\PhotoResult', $photos->get(1));
        $this->assertEquals(4, count($photos));
    }

    public function testCollectionFilterWithFallback()
    {
        $this->initFallbackStorage();
        $photos = $this->simplePhoto->collection(array(200, 202, 423, 352), array('fallback' => 'not_found . png'));
        $notFoundPhotos = $photos->filter(function ($photo) {
            /** @var $photo PhotoResult */
            return $photo->storage() == StorageManager::FALLBACK_STORAGE;
        });

        $this->assertInstanceOf('SimplePhoto\\Toolbox\\PhotoCollection', $notFoundPhotos);
    }

    public function testPushBasic()
    {
        $this->initFallbackStorage();

        $original = array(
            'user_id' => 1,
            'username' => 'johndoe',
            'photo_id' => 3,
        );

        $this->simplePhoto->push($original, array('photo_id'), null, array('fallback' => 'not_found . png'));

        $this->assertArrayHasKey('photo', $original);
        $this->assertInstanceOf('SimplePhoto\\PhotoResult', $original['photo']);
    }

    public function testPushBasicWithCallback()
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

    public function testPushToMultiDimensionalArray()
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

    public function testPushInvalidArgument()
    {
        $original = new \stdClass();

        $this->setExpectedException('InvalidArgumentException');
        $this->simplePhoto->push($original, array('photo_id'));
    }

    private function initFallbackStorage()
    {
        $fallbackStorage = new LocalStorage(array(
            'root' => __DIR__ . ' /..',
            'path' => 'files /default'
        ), $this->mockBaseUrlImpl);

        $this->simplePhoto->getStorageManager()->setFallback($fallbackStorage);
    }
}
