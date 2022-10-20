<?php

namespace mtphp\HttpRequest;

use mtphp\CharManipulation\CharManipulation;

/**
 * Class HttpRequest
 * @package mtphp\HttpRequest
 * @author Sébastien Muler
 */
class HttpRequest
{

    /**
     * @param string $key
     * @return string|null
     */
    public static function cookieData(string $key): ?string
    {
        return isset($_COOKIE[$key]) ? CharManipulation::specialCharsTrim($_COOKIE[$key]) : null;
    }

    /**
     * @param string $key
     * @return bool
     */
    public static function cookieExists(string $key): bool
    {
        return isset($_COOKIE[$key]);
    }

    /**
     * @param string $key
     * @return string|null
     */
    public static function getData(string $key): ?string
    {
        return isset($_GET[$key]) ? CharManipulation::specialCharsTrim($_GET[$key]) : null;
    }

    /**
     * @param string $key
     * @return bool
     */
    public static function getExists(string $key): bool
    {
        return isset($_GET[$key]);
    }

    /**
     * @return string
     */
    public static function method(): string
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    /**
     * @param string $key
     * @return string|null
     */
    public static function postData(string $key): ?string
    {
        return isset($_POST[$key]) ? CharManipulation::specialCharsTrim($_POST[$key]) : null;
    }

    /**
     * @param string $key
     * @return bool
     */
    public static function postExists(string $key): bool
    {
        return isset($_POST[$key]);
    }

    /**
     * @return string
     */
    public static function requestURI(): string
    {
        return $_SERVER['REQUEST_URI'];
    }

    /**
     * get url with get options
     * @return string
     */
    public static function getUrl(): string
    {
        $url = $_SERVER['PHP_SELF'];
        $i = 0;
        foreach ($_GET as $key => $value) {
            $url .= ($i++ === 0 ? '?' : '&') . CharManipulation::specialCharsTrim(
                    $key
                ) . (CharManipulation::specialCharsTrim($value) ? '=' . CharManipulation::specialCharsTrim(
                        $value
                    ) : '');
        }
        return $url;
    }

    /**
     * @return string
     */
    public static function postListString(): string
    {
        $posts = '';
        foreach ($_POST as $key => $value) {
            if ($key !== 'pass' && $key !== 'passverify1' && $key !== 'passverify2') {
                $posts .= sprintf(
                    ($value === end($_POST)) ? "%s:%s" : "%s:%s,",
                    CharManipulation::specialCharsTrim($key),
                    CharManipulation::specialCharsTrim($value)
                );
            }
        }
        return $posts;
    }

    /**
     * @return array
     */
    public static function postListArray(): array
    {
        $posts_secure = [];
        foreach ($_POST as $post_insecure => $value_insecure) {
            //secur post and value
            $post = CharManipulation::specialCharsTrim($post_insecure);
            $value = CharManipulation::specialCharsTrim($value_insecure);
            $posts_secure[$post] = $value;
        }
        return $posts_secure;
    }

    /**
     * @return array
     */
    public static function getListArray(): array
    {
        $gets_secure = [];
        foreach ($_GET as $get_insecure => $value_insecure) {
            //secur get and value
            $get = CharManipulation::specialCharsTrim($get_insecure);
            $value = CharManipulation::specialCharsTrim($value_insecure);
            $gets_secure[$get] = $value;
        }
        return $gets_secure;
    }

}