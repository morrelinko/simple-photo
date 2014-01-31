<?php

namespace SimplePhoto;

/**
 * @author Laju Morrison <morrelinko@gmail.com>
 */
class StorageManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var StorageManager
     */
    protected $storageManager;

    public function setUp()
    {
        $this->storageManager = new StorageManager();
    }

    public function testGetUndefinedStorage()
    {
        $this->setExpectedException('RuntimeException');
        $this->storageManager->get('invalid_storage');
    }

    public function testAddAndRemoveStorage()
    {
        $mockStorage = \Mockery::mock('SimplePhoto\Storage\StorageInterface');
        $this->storageManager->add('mock_storage', $mockStorage);

        $this->assertTrue($this->storageManager->has('mock_storage'));

        $this->storageManager->remove('mock_storage');
        $this->assertFalse($this->storageManager->has('mock_storage'));
    }
}
