<?php

/*
 * This file is part of the SimplePhoto package.
 *
 * (c) Laju Morrison <morrelinko@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SimplePhoto\Source;

/**
 * @author Laju Morrison <morrelinko@gmail.com>
 */
class UrlSource implements PhotoSourceInterface
{
    /**
     * @var string
     */
    protected $url;

    /**
     * @var string
     */
    protected $path;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $mime;

    /**
     * @var bool
     */
    protected $valid = true;

    public function __construct($url)
    {
        $this->url = $url;
    }

    /**
     * {@inheritDoc}
     */
    public function process(array $options = array())
    {
        $this->name = basename($this->url);
        $this->path = tempnam($options['tmp_dir'], 'sp');
        $fp = fopen($this->path, 'w+');

        $ch = curl_init($this->url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_FILE, $fp);

        curl_exec($ch);
        $headers = curl_getinfo($ch);

        curl_close($ch);
        fclose($fp);

        if ($headers['http_code'] === 200 && $headers['download_content_length'] > 0) {
            $this->mime = $headers['content_type'];

            return $this;
        }

        $this->valid = false;
        unlink($this->path);

        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritDoc}
     */
    public function getFile()
    {
        return $this->path;
    }

    /**
     * {@inheritDoc}
     */
    public function getMime()
    {
        return $this->mime;
    }

    /**
     * {@inheritDoc}
     */
    public function isValid()
    {
        return $this->valid;
    }
}
