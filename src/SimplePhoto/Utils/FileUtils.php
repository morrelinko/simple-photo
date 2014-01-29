<?php

/*
 * This file is part of the SimplePhoto package.
 *
 * (c) Laju Morrison <morrelinko@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SimplePhoto\Utils;

/**
 * @author Laju Morrison <morrelinko@gmail.com>
 */
class FileUtils
{
    /**
     * Normalises a path. [php.net]function.realpath.html#84012
     * - Converts backslashes to forward slash
     * - Removes multiple slashes
     * - Parses ../ and ./ paths fine
     *
     * @param string $path
     * @param boolean $fixAbsolute
     *
     * @return string
     */
    public static function normalizePath($path, $fixAbsolute = true)
    {
        $path = str_replace('\\', '/', $path);
        $parts = array_filter(explode('/', $path), 'strlen');
        $isWin = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
        $realPath = (self::isAbsolute($path) && !$isWin && $fixAbsolute) ? '/' : '';
        $fixedParts = array();

        foreach ($parts as $part) {
            if ($part == '.') {
                continue;
            }

            if ($part == '..') {
                array_pop($fixedParts);
            } else {
                $fixedParts[] = $part;
            }
        }

        $realPath .= implode('/', $fixedParts);

        return $realPath;
    }

    /**
     * Checks if a path is an absolute path
     *
     * @param string $path
     *
     * @return bool
     */
    public static function isAbsolute($path)
    {
        return ((isset($path[0])
                ? ($path[0] == '/'
                    || (ctype_alpha($path[0]) && ($path[1] == ':')))
                : '')
            || (parse_url($path, PHP_URL_SCHEME) === true))
            ? true
            : false;
    }
}
