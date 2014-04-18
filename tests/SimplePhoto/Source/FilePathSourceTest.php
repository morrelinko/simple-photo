<?php

namespace SimplePhoto\Source;

/**
 * @author Laju Morrison <morrelinko@gmail.com>
 */
class FilePathSourceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var FilePathSource
     */
    protected $source;

    public function setUp()
    {
        $this->source = new FilePathSource(__DIR__ . '/../../files/tmp/sample.png');
        $this->source->process();
    }

    public function testGetSourceAttributes()
    {
        $this->assertEquals('sample.png', $this->source->getName());
        $this->assertEquals(__DIR__ . '/../../files/tmp/sample.png', $this->source->getFile());
        $this->assertEquals('image/png', $this->source->getMime());
    }

    public function testSourceValidation()
    {
        $anotherSource = new FilePathSource('test/notfound.png');

        $this->assertTrue($this->source->isValid());
        $this->assertFalse($anotherSource->isValid());
    }
}
