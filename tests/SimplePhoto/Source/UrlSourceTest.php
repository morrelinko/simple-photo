<?php

namespace SimplePhoto\Source;

function curl_init($url = null)
{
    if (parse_url($url, PHP_URL_HOST) == 'example.com') {
        return 'test';
    }

    if (parse_url($url, PHP_URL_HOST) == 'fail.example.com') {
        return 'test.fail';
    }

    return \curl_init($url);
}

function curl_setopt($ch, $option, $value)
{
    if ($ch == 'test' || $ch == 'test.fail') {
        return true;
    }

    return \curl_setopt($ch, $option, $value);
}

function curl_exec($ch)
{
    if ($ch == 'test' || $ch == 'test.fail') {
        return true;
    }

    return \curl_exec($ch);
}

function curl_close($ch)
{
    if ($ch == 'test' || $ch == 'test.fail') {
        return true;
    }

    return \curl_close($ch);
}

function curl_getinfo($ch)
{
    if ($ch == 'test') {
        return array(
            'http_code' => 200,
            'download_content_length' => 24342,
            'content_type' => 'image/png'
        );
    }

    if ($ch == 'test.fail') {
        return array(
            'http_code' => 404,
            'download_content_length' => 0,
            'content_type' => null
        );
    }

    return \curl_getinfo($ch);
}

/**
 * @author Laju Morrison <morrelinko@gmail.com>
 */
class UrlSourceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var UrlSource
     */
    protected $source;

    public function setUp()
    {
        $this->source = new UrlSource('http://example.com/files/photo.png');
        $this->source->process(array('tmp_dir' => __DIR__ . '/../files/tmp'));
    }

    public function testGetSourceAttributes()
    {
        $this->assertEquals('photo.png', $this->source->getName());
        $this->assertFileExists($this->source->getFile());
        $this->assertEquals('image/png', $this->source->getMime());
    }

    public function testSourceValidation()
    {
        $anotherSource = new UrlSource('http://fail.example.com/files/photo.png');
        $anotherSource->process(array('tmp_dir' => __DIR__ . '/../files/tmp'));

        $this->assertFalse($anotherSource->isValid());
        $this->assertTrue($this->source->isValid());
    }
}
