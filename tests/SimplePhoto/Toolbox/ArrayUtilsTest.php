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
}
