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
     * {@inheritDoc}
     */
    public function getBaseUrl()
    {
        $secure = (isset($_SERVER['HTTPS']) && filter_var($_SERVER['HTTPS'], FILTER_VALIDATE_BOOLEAN)) ||
            ($_SERVER['SERVER_PORT'] == 443);

        $host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';
        $path = str_replace('\\', '/', pathinfo($_SERVER['PHP_SELF'], PATHINFO_DIRNAME));

        return 'http' . ($secure ? 's' : '') . '://' . $host . ($path == '/' ? '' : '/' . trim($path, '/'));
    }
}
