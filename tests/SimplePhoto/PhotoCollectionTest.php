<?php

namespace SimplePhoto;

use SimplePhoto\Toolbox\PhotoCollection;

/**
 * @author Laju Morrison <morrelinko@gmail.com>
 */
class PhotoCollectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var PhotoCollection
     */
    protected $collection;

    public function setUp()
    {
        $this->collection = new PhotoCollection(array(
            array(
                'id' => '1',
                'name' => '001.png',
                'storage' => 'local'
            ),
            array(
                'id' => '2',
                'name' => '002.png',
                'storage' => 'static_img_host'
            )
        ));
    }

    public function testAddItem()
    {
        $this->assertCount(2, $this->collection);

        $this->collection->push(array(
            'id' => '3',
            'name' => '003.png',
            'storage' => 'local'
        ));

        $this->assertCount(3, $this->collection);
    }

    public function testMiscellaneous()
    {
        $this->assertFalse($this->collection->isEmpty());
        $this->assertNull($this->collection->get(5));
    }

    public function testLists()
    {
        $expected = array('001.png', '002.png');
        $actual = $this->collection->lists(function ($item) {
            return $item['name'];
        });

        $this->assertSame($expected, $actual);
    }
}
