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
     * @var array
     */
    protected static $mimes = array(
        'jpeg' => 'image/jpeg',
        'jpg' => 'image/jpeg',
        'png' => 'image/png',
        'gif' => 'image/gif',
        'tiff' => 'image/tiff',
        'tif' => 'image/tiff'
    );

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

    /**
     * @param string $file
     *
     * @return string
     */
    public static function createTempFile($file)
    {
        $tmpFile = tempnam(sys_get_temp_dir(), 'up');
        copy($file, $tmpFile);

        return $tmpFile;
    }

    /**
     * Gets the mime of a file
     *
     * @param string $file
     *
     * @return mixed
     */
    public static function getMime($file)
    {
        $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($fileInfo, $file);

        return empty($mime) ? : $mime;
    }

    /**
     * @param string $extension
     *
     * @return null|string
     */
    public static function getMimeFromExtension($extension)
    {
        if (isset(static::$mimes[$extension])) {
            return static::$mimes[$extension];
        }

        return null;
    }

    /**
     * Gets the extension of a file
     *
     * @param string $file
     *
     * @return string
     */
    public static function getExtension($file)
    {
        return pathinfo($file, PATHINFO_EXTENSION);
    }

    /**
     * @param $mime
     *
     * @return null|string
     */
    public static function getExtensionFromMime($mime)
    {
        $mimes = array_flip(static::$mimes);
        if (isset($mimes[$mime])) {
            return $mimes[$mime];
        }

        return null;
    }
}
