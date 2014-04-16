<?php

namespace SimplePhoto\Toolbox;

/**
 * @author Laju Morrison <morrelinko@gmail.com>
 */
class ArrayUtilsTest extends \PHPUnit_Framework_TestCase
{
    public function testGetFirstArrayElement()
    {
        $array = array('Spam', 323, new \stdClass());

        $this->assertEquals('Spam', ArrayUtils::first($array));
    }

    public function testEnsureArrayHasKeys()
    {
        $array = array('name' => 'John Doe', 'age' => 24);

        $this->assertTrue(ArrayUtils::hasKeys($array, 'name', 'age'));
        $this->assertFalse(ArrayUtils::hasKeys($array, 'name', 'age', 'height'));
    }

    public function testArrayColumn()
    {
        $array = array(
            array('name' => 'John Doe', 'age' => 24),
            array('name' => 'Alice Jane', 'age' => 20),
            array('name' => 'Bob Joe', 'age' => 26),
        );

        $this->assertEquals(
            array('John Doe', 'Alice Jane', 'Bob Joe'),
            ArrayUtils::arrayColumn($array, 'name')
        );

        $this->assertEquals(
            array(24, 20, 26),
            ArrayUtils::arrayColumn($array, 'age')
        );
    }
}
