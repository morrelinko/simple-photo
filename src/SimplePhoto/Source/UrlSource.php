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
    protected $path;

    protected $name;

    public function __construct($url)
    {
        if ($url != null) {
            $this->process($url);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function process($url)
    {
        $this->name = basename($url);
        $this->path = tempnam(sys_get_temp_dir(), 'sp_url');
        $fp = fopen($this->path, 'w+');

        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_FILE, $fp);

        curl_exec($ch);

        curl_close($ch);
        fclose($fp);
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
}
