<?php

namespace SimplePhoto\Toolbox;

/**
 * @author Laju Morrison <morrelinko@gmail.com>
 */
class PhotoCollectionTest extends \PHPUnit_Framework_TestCase
{
    public function testCollection()
    {
        $collection = new PhotoCollection(array(array('id' => 23434)));
        $collection->push(array('id' => 643534));
        $collection->ksort();

        $this->assertCount(2, $collection);
        $this->assertEquals(array(array('id' => 23434), array('id' => 643534)), $collection->all());
        $this->assertEquals(array('id' => 643534), $collection->get(1));
        $this->assertNull($collection->get(4));
        $this->assertTrue($collection->has(0));
    }

    public function testTransform()
    {
        $collection = new PhotoCollection();
        $collection->push(array('id' => 643534));

        $collection->transform(function ($item) {
            return (object) $item;
        });

        $this->assertInstanceOf('stdClass', $collection->get(0));
    }

    public function testFilter()
    {
        $collection = new PhotoCollection();
        $collection->push(array('id' => 6374, 'storage' => 'local'));
        $collection->push(array('id' => 643534, 'storage' => 'remote'));

        $localCollection = $collection->filter(function ($item) {
            return $item['storage'] == 'local';
        });

        $this->assertCount(2, $collection);
        $this->assertCount(1, $localCollection);

        $photo = $localCollection->get(0);
        $this->assertEquals(6374, $photo['id']);
    }

    public function testLists()
    {
        $collection = new PhotoCollection();
        $collection->push(array('id' => 6374, 'storage' => 'local'));
        $collection->push(array('id' => 643534, 'storage' => 'remote'));

        $lists = $collection->lists(function ($item) {
            return $item['id'];
        });

        $this->assertEquals(array(6374, 643534), $lists);
    }

    public function testIsEmpty()
    {
        $collection = new PhotoCollection();
        $this->assertTrue($collection->isEmpty());
        $collection->push(array('id' => 6374, 'storage' => 'local'));
        $this->assertFalse($collection->isEmpty());
    }
}
