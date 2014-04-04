<?php

namespace SimplePhoto\Storage;

    /**
     * Functions to mock
     * - ftp_connect
     * - ftp_login,
     * - ftp_put
     * - ftp_delete
     * - ftp_get
     * - ftp_rawlist
     * - ftp_close
     * - ftp_chdir
     * - ftp_mkdir
     */

/**
 * @param $host
 * @return bool
 */
function ftp_connect($host)
{
    if ($host != '127.0.0.1') {
        return null;
    }

    return 'resource';
}

/**
 * @param $ftp_stream
 * @param $username
 * @param $password
 * @return bool
 */
function ftp_login($ftp_stream, $username, $password)
{
    if ($ftp_stream != 'resource') {
        return false;
    }

    if ($username != 'user' || $password != 'xyz') {
        return false;
    }

    return true;
}

/**
 * @return bool
 */
function ftp_close()
{
    return true;
}

/**
 * @param $ftp_stream
 * @param $directory
 * @return bool
 */
function ftp_chdir($ftp_stream, $directory)
{
    if (strpos($directory, 'fails') !== false) {
        return false;
    }

    return true;
}

/**
 * @param $ftp_stream
 * @param $local_file
 * @param $remote_file
 * @return bool
 */
function ftp_get($ftp_stream, $local_file, $remote_file)
{
    if (strpos($remote_file, 'fails') !== false) {
        return false;
    }

    file_put_contents($local_file, 'contents');

    return true;
}

function ftp_file($ftp_stream, $remote_file)
{
    return 117689775;
}

/**
 * @param $ftp_stream
 * @param $remote_file
 * @param $local_file
 * @return bool
 */
function ftp_put($ftp_stream, $remote_file, $local_file)
{
    if (strpos($remote_file, 'fails') !== false) {
        return false;
    }

    return true;
}

/**
 * @param $ftp_stream
 * @param $path
 * @return bool
 */
function ftp_delete($ftp_stream, $path)
{
    if (strpos($path, 'fails') !== false) {
        return false;
    }

    return true;
}

/**
 * @param $ftp_stream
 * @param $directory
 * @return bool
 */
function ftp_mkdir($ftp_stream, $directory)
{
    if (strpos($directory, 'fails') !== false) {
        return false;
    }

    return true;
}

/**
 * @param $ftp_stream
 * @param $directory
 * @return array
 */
function ftp_rawlist($ftp_stream, $directory)
{
    return array(
        'drwxr-xr-x   4 ftp      ftp          4096 Nov 24 13:58 .',
        'drwxr-xr-x  16 ftp      ftp          4096 Sep  2 13:01 ..',
        'drwxr-xr-x   2 ftp      ftp          4096 Oct 13  2012 cgi-bin',
        'drwxr-xr-x   2 ftp      ftp          4096 Nov 24 13:59 public_html',
        '-rw-r--r--   1 ftp      ftp           409 Oct 13  2012 file.png',
        '-rw-r--r--   1 ftp      ftp           409 Oct 13  2012 fails.png',
        '',
        'www/cgi-bin:',
        'drwxr-xr-x   2 ftp      ftp          4096 Oct 13  2012 .',
        'drwxr-xr-x   4 ftp      ftp          4096 Nov 24 13:58 ..',
        '',
        'www/public_html:',
        'drwxr-xr-x   2 ftp      ftp          4096 Nov 24 13:59 .',
        'drwxr-xr-x   4 ftp      ftp          4096 Nov 24 13:58 ..',
        '-rw-r--r--   1 ftp      ftp             0 Nov 24 13:59 dummy.txt',
    );
}