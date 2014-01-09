<?php namespace SimplePhoto\Utils;

/**
 * @author Morrison Laju <morrelinko@gmail.com>
 */
class RequestUtils
{
    protected static $baseUrl;

    protected static $basePath;

    public static function getBaseUrl()
    {
        $basePath = pathinfo($_SERVER['PHP_SELF'], PATHINFO_DIRNAME);

        return "http" . (self::isSecure() ? "s" : "") . "://" . self::getHost() . ($basePath == "/" ? "" : "/" . trim($basePath, "/"));
    }

    public static function getHost()
    {
        return isset($_SERVER["HTTP_HOST"]) ? $_SERVER["HTTP_HOST"] : "";
    }

    public static function isSecure()
    {
        if ((isset($_SERVER["HTTPS"]) AND filter_var($_SERVER["HTTPS"], FILTER_VALIDATE_BOOLEAN)) ||
            ($_SERVER["SERVER_PORT"] == 443)
        ) {
            return true;
        }

        return false;
    }
}
