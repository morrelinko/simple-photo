<?php namespace SimplePhoto\Storage;

use SimplePhoto\Utils\FileUtils;

/**
 * @author Morrison Laju <morrelinko@gmail.com>
 */
class LocalStorageTest extends \PHPUnit_Framework_TestCase
{
    const TEST_UPLOAD_FILE = "uploaded_file.txt";

    const BASE_URL = "http://example.com";

    /**
     * @var LocalStorage
     */
    protected $storage;

    protected $projectPath;

    protected $savePath;

    public function setUp()
    {
        $this->projectPath = __DIR__ . "/../..";
        $this->savePath = "files/photo";

        $mockBaseUrlImpl = \Mockery::mock("SimplePhoto\\Toolbox\\BaseUrlInterface");
        $mockBaseUrlImpl->shouldReceive("getBaseUrl")->andReturn(self::BASE_URL);

        $this->storage = new LocalStorage($this->projectPath, $this->savePath, $mockBaseUrlImpl);
    }

    public function tearDown()
    {
        $this->storage = null;
        $uploadFile = FileUtils::normalizePath(
            $this->projectPath . "/" . $this->savePath . "/" . self::TEST_UPLOAD_FILE);
        if (file_exists($uploadFile)) {
            unlink($uploadFile);
        }
    }

    public function testPath()
    {
        $this->assertFileExists($this->storage->getPath());
    }

    public function testGetPhotoData()
    {
        $photoUrl = self::BASE_URL . "/" . $this->savePath . "/" . self::TEST_UPLOAD_FILE;
        $photoPath = FileUtils::normalizePath(
                $this->projectPath . "/" . $this->savePath) . "/" . self::TEST_UPLOAD_FILE;

        // Test getPhotoUrl()
        $this->assertSame($photoUrl, $this->storage->getPhotoUrl(self::TEST_UPLOAD_FILE));

        // Test getPhotoPath()
        $this->assertSame($photoPath, $this->storage->getPhotoPath(self::TEST_UPLOAD_FILE));
    }

    public function testUploadFile()
    {
        $file = tempnam(sys_get_temp_dir(), null);
        $this->storage->upload($file, self::TEST_UPLOAD_FILE);

        $this->assertFileExists(FileUtils::normalizePath(
                $this->projectPath . "/" . $this->savePath) . "/" . self::TEST_UPLOAD_FILE);
    }
}
