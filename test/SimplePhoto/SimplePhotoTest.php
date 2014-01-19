<?php namespace SimplePhoto;

use SimplePhoto\DataStore\DataStoreInterface;
use SimplePhoto\DataStore\SqliteDataStore;
use SimplePhoto\Source\FilePathSource;
use SimplePhoto\Storage\LocalStorage;

/**
 * @author Morrison Laju <morrelinko@gmail.com>
 */
class SimplePhotoTest extends \PHPUnit_Framework_TestCase
{
    const BASE_URL = "http://example.com";

    /**
     * @var SimplePhoto
     */
    protected $simplePhoto;

    public function setUp()
    {
        $mockBaseUrlImpl = \Mockery::mock("SimplePhoto\\Toolbox\\BaseUrlInterface");
        $mockBaseUrlImpl->shouldReceive("getBaseUrl")->andReturn(self::BASE_URL);

        // Setup
        $localStorageOne = new LocalStorage(__DIR__ . "/..", "files/photo/", $mockBaseUrlImpl);
        $localStorageTwo = new LocalStorage(__DIR__ . "/..", "files/photo_2/", $mockBaseUrlImpl);

        // TODO Mock DataStore
        $dataStore = new SqliteDataStore(array(
            "database" => __DIR__ . "/../files/database/test_photos.db"
        ));
        $this->createTestDatabase($dataStore);

        $storageManager = new StorageManager();
        $storageManager->add("local_storage_1", $localStorageOne);
        $storageManager->add("local_storage_2", $localStorageTwo);

        $this->simplePhoto = new SimplePhoto($storageManager, $dataStore);
    }

    public function tearDown()
    {
        $this->simplePhoto = null;
        \Mockery::close();
    }

    public function testStorageManager()
    {
        $storageManager = $this->simplePhoto->getStorageManager();
        $this->assertInstanceOf("SimplePhoto\\StorageManager", $storageManager);

        $this->assertArrayHasKey("local_storage_1", $storageManager->getAll());
        $this->assertArrayHasKey("local_storage_2", $storageManager->getAll());

        $this->assertSame("local_storage_1", $storageManager->getDefault());
        $storageManager->setDefault("local_storage_2");
        $this->assertSame("local_storage_2", $storageManager->getDefault());

        $this->assertContainsOnlyInstancesOf(
            "SimplePhoto\\Storage\\StorageInterface", $storageManager->getAll());
        $this->assertInstanceOf(
            "SimplePhoto\\Storage\\StorageInterface", $storageManager->get("local_storage_1"));
    }

    public function testDataStore()
    {
        $dataStore = $this->simplePhoto->getDataStore();
        $this->assertInstanceOf("SimplePhoto\\DataStore\\DataStoreInterface", $dataStore);
    }

    public function testUploadPhoto()
    {
        $photoId = $this->simplePhoto->uploadFromFilePath($this->createPhotoSourceFile());
        $this->assertTrue($photoId > 0);
    }

    public function testUploadAndTransformSize()
    {
        // Same as $this->simplePhoto->uploadFromFilePath()
        $photoId = $this->simplePhoto->uploadFrom(
            $this->createPhotoSourceFile(), array(), new FilePathSource());
        $photo = $this->simplePhoto->get($photoId, array(
            "transform" => array(
                "size" => array(50, 50)
            )
        ));

        $this->assertNotNull($photo->originalFilePath());

        list($origWidth, $origHeight) = getimagesize($this->createPhotoSourceFile());
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
            "size" => array(100, 100)
        );

        $photoId = $this->simplePhoto->uploadFromFilePath(
            $this->createPhotoSourceFile(), array(
                "transform" => $transform
            )
        );

        $photo = $this->simplePhoto->get($photoId);
        list($width, $height) = getimagesize($photo->path());

        $this->assertEquals(100, $width);
        $this->assertEquals(100, $height);
    }

    public function testGetPhoto()
    {
        $photoId = $this->simplePhoto->uploadFromFilePath($this->createPhotoSourceFile());

        $photo = $this->simplePhoto->get($photoId);
        $this->assertInstanceOf("SimplePhoto\\PhotoResult", $photo);

        $this->assertSame($photo->id(), $photoId);
        $this->assertSame($photo->fileName(), "image_group.png");
        $this->assertFileExists($photo->path());
    }

    public function testGetInvalidPhoto()
    {
        $photo = $this->simplePhoto->get(5000000);
        $this->assertFalse($photo);
    }

    private function createPhotoSourceFile()
    {
        return __DIR__ . "/../files/tmp/image_group.png";
    }

    private function createTestDatabase(DataStoreInterface $dataStore)
    {
        $dataStore->getConnection()->exec("
            CREATE TABLE IF NOT EXISTS photos (
                photo_id INTEGER PRIMARY KEY,
                storage_name TEXT NOT NULL,
                file_name TEXT NOT NULL,
                file_extension TEXT NOT NULL,
                file_path TEXT NOT NULL,
                file_mime TEXT NOT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );
        ");
    }
}
