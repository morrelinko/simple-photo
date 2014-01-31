<?php

namespace SimplePhoto\Toolbox;

/**
 * @author Laju Morrison <morrelinko@gmail.com>
 */
class HttpBaseUrlTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var HttpBaseUrl
     */
    protected $httpBaseUrlImpl;

    public function setUp()
    {
        $_SERVER = array(
            'SERVER_PORT' => 80,
            'HTTP_HOST' => 'example.com',
            'PHP_SELF' => '/test/index.php'
        );

        $this->httpBaseUrlImpl = new HttpBaseUrl();
    }

    public function testGetBaseUrl()
    {
        $this->assertSame('http://example.com/test', $this->httpBaseUrlImpl->getBaseUrl());
    }

    public function testGetBaseUrlHttps()
    {
        $_SERVER['HTTPS'] = 'on';
        $this->assertSame('https://example.com/test', $this->httpBaseUrlImpl->getBaseUrl());
    }
}
