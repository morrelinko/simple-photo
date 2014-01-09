<?php namespace SimplePhoto\Utils;

/**
 * @author Morrison Laju <morrelinko@gmail.com>
 */
class TextUtils
{
    public static function endsWith($text, $char)
    {
        return substr($text, -(strlen($char))) == $char;
    }
} 