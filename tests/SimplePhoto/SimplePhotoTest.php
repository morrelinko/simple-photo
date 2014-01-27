<?php namespace SimplePhoto;

use SimplePhoto\DataStore\SqliteDataStore;
use SimplePhoto\Source\FilePathSource;
use SimplePhoto\Storage\LocalStorage;
use SimplePhoto\Utils\FileUtils;

/**
 * @author Laju Morrison <morrelinko@gmail.com>
 */
class SimplePhotoTest extends \PHPUnit_Framework_TestCase
{
    const BASE_URL = "http://example.com";

    const CREATE_PHOTO_TABLE = '
        CREATE TABLE IF NOT EXISTS photo (
            photo_id INTEGER PRIMARY KEY,
            storage_name TEXT NOT NULL,
            file_name TEXT NOT NULL,
            file_extension TEXT NOT NULL,
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
        $localStorageOne = new LocalStorage(__DIR__ . '/..', 'files/photo/', $this->mockBaseUrlImpl);
        $localStorageTwo = new LocalStorage(__DIR__ . '/..', 'files/avatars/', $this->mockBaseUrlImpl);

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
            printf("Files Directory contained a directory we can not recurse into");
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

    public function testUploadAndTransformSize()
    {
        // Same as $this->simplePhoto->uploadFromFilePath()
        $photoId = $this->simplePhoto->uploadFrom(
            $this->photoSourceFile, new FilePathSource(), array());
        $photo = $this->simplePhoto->get($photoId, array(
            'transform' => array(
                'size' => array(50, 50)
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
            'size' => array(100, 100)
        );

        $photoId = $this->simplePhoto->uploadFromFilePath(
            $this->photoSourceFile, array(
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
        $photoId = $this->simplePhoto->uploadFromFilePath($this->photoSourceFile);

        $photo = $this->simplePhoto->get($photoId);
        $this->assertInstanceOf('SimplePhoto\\PhotoResult', $photo);

        $this->assertSame($photo->id(), $photoId);
        $this->assertSame($photo->fileName(), 'image_group.png');
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
        $photo = $this->simplePhoto->get(5000000, array('fallback' => 'not_found.png'));
        $this->assertNotSame(false, $photo);
    }

    public function testCollectionWithFallback()
    {
        $this->initFallbackStorage();
        $photos = $this->simplePhoto->collection(array(1, 2, 3, 4), array('fallback' => 'not_found.png'));
        $this->assertContainsOnlyInstancesOf('SimplePhoto\\PhotoResult', $photos->all());
        $this->assertInstanceOf('SimplePhoto\\PhotoResult', $photos->get(1));
        $this->assertEquals(4, count($photos));
    }

    public function testCollectionFilterWithFallback()
    {
        $this->initFallbackStorage();
        $photos = $this->simplePhoto->collection(array(200, 202, 423, 352), array('fallback' => 'not_found.png'));
        $notFoundPhotos = $photos->filter(function ($photo) {
            /** @var $photo PhotoResult */
            return $photo->storage() == StorageManager::FALLBACK_STORAGE;
        });

        $this->assertInstanceOf('SimplePhoto\\Toolbox\\PhotoCollection', $notFoundPhotos);
    }

    private function initFallbackStorage()
    {
        $fallbackStorage = new LocalStorage(__DIR__ . '/..', 'files/default', $this->mockBaseUrlImpl);
        $this->simplePhoto->getStorageManager()->setFallback($fallbackStorage);
    }
}
