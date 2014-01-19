<?php

/*
 * This file is part of the SimplePhoto package.
 *
 * (c) Laju Morrison <morrelinko@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SimplePhoto\Toolbox;

/**
 * Default Implementation for retrieving base url
 *
 * @author Laju Morrison <morrelinko@gmail.com>
 */
class HttpBaseUrl implements BaseUrlInterface
{
    /**
     * {@inheritDocs}
     */
    public function getBaseUrl()
    {
        $basePath = pathinfo($_SERVER['PHP_SELF'], PATHINFO_DIRNAME);

        return 'http' . ($this->isSecure() ? 's' : '') . '://' . $this->getHost() . ($basePath == '/' ? '' : '/' . trim($basePath, '/'));
    }

    /**
     * @return string
     */
    public function getHost()
    {
        return isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';
    }

    /**
     * @return bool
     */
    public function isSecure()
    {
        if ((isset($_SERVER['HTTPS']) AND filter_var($_SERVER['HTTPS'], FILTER_VALIDATE_BOOLEAN)) ||
            ($_SERVER['SERVER_PORT'] == 443)
        ) {
            return true;
        }

        return false;
    }
}
