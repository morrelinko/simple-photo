<?php

namespace SimplePhoto;

use SimplePhoto\Storage\LocalStorage;
use SimplePhoto\Storage\MemoryStorage;

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

    public function testGetAllStorage()
    {
        $this->storageManager->add('core', new MemoryStorage());
        $this->storageManager->add('storage', new LocalStorage());

        $this->assertCount(2, $this->storageManager->getAll());
        $this->assertContainsOnlyInstancesOf('SimplePhoto\Storage\StorageInterface', $this->storageManager->getAll());
    }

    public function testFallback()
    {
        $this->storageManager->setFallback(new MemoryStorage());

        $this->assertTrue($this->storageManager->hasFallback());
    }

    public function testUsesFirstStorageAsDefaultIfNone()
    {
        $this->storageManager->add('core', new MemoryStorage());
        $this->storageManager->add('storage', new LocalStorage());

        $this->assertEquals('core', $this->storageManager->getDefault());
    }

    public function testUseDefinedDefaultStorage()
    {
        $this->storageManager->add('core', new MemoryStorage());
        $this->storageManager->add('storage', new LocalStorage());
        $this->storageManager->setDefault('storage');

        $this->assertEquals('storage', $this->storageManager->getDefault());
    }

    public function testGetUndefinedStorage()
    {
        $this->setExpectedException('RuntimeException');
        $this->storageManager->get('invalid_storage');
    }

    public function testAddStorageThatAlreadyExists()
    {
        $this->setExpectedException('RuntimeException');
        $this->storageManager->add('core', new MemoryStorage());
        $this->storageManager->add('core', new LocalStorage());
    }

    public function testReplaceExistingStorage()
    {
        $this->storageManager->add('core', new MemoryStorage());
        $this->storageManager->replace('core', new LocalStorage());

        $this->assertInstanceOf('SimplePhoto\Storage\LocalStorage', $this->storageManager->get('core'));
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
