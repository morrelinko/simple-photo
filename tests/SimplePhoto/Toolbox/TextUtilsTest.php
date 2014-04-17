<?php

namespace SimplePhoto\Toolbox;

/**
 * @author Laju Morrison <morrelinko@gmail.com>
 */
class TextUtilsTest extends \PHPUnit_Framework_TestCase
{
    public function testEndsWith()
    {
        $this->assertFalse(TextUtils::endsWith('Morrelinko', 'M'));
        $this->assertTrue(TextUtils::endsWith('Morrelinko', 'o'));
        $this->assertTrue(TextUtils::endsWith('Morrelinko', 'linko'));
        $this->assertFalse(TextUtils::endsWith('Morrelinko', 'relink'));
    }
}
