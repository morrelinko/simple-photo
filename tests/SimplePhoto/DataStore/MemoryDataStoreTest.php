<?php

namespace SimplePhoto\DataStore;

/**
 * @author Laju Morrison <morrelinko@gmail.com>
 */
class MemoryDataStoreTest extends \PHPUnit_Framework_TestCase
{
    public function testConnection()
    {
        $memory = new MemoryDataStore();

        $this->assertInternalType('array', $memory->getConnection());
    }
}
