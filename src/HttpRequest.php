<?php

namespace MulerTech\HttpRequest;

use MulerTech\CharManipulation\CharManipulation;

/**
 * Class HttpRequest
 * @package MulerTech\HttpRequest
 * @author Sébastien Muler
 */
class HttpRequest
{
    /**
     * @param string $key
     * @return string|null
     */
    public static function getCookie(string $key): string|null
    {
        if (!isset($_COOKIE[$key])) {
            return null;
        }

        /** @var string $cookie For PHPStan */
        $cookie = CharManipulation::specialCharsTrim($_COOKIE[$key]);
        return $cookie;
    }

    /**
     * @param string $key
     * @return bool
     */
    public static function hasCookie(string $key): bool
    {
        return isset($_COOKIE[$key]);
    }

    /**
     * @param string $key
     * @return string|null
     */
    public static function get(string $key): string|null
    {
        if (!isset($_GET[$key])) {
            return null;
        }

        /** @var string $get For PHPStan */
        $get = CharManipulation::specialCharsTrim($_GET[$key]);
        return $get;
    }

    /**
     * @param string $key
     * @return bool
     */
    public static function has(string $key): bool
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
     * @return string|array<int|string, mixed>|null
     */
    public static function getPost(string $key): string|array|null
    {
        if (!isset($_POST[$key])) {
            return null;
        }

        /** @var string $post For PHPStan */
        $post = CharManipulation::specialCharsTrim($_POST[$key]);
        return $post;
    }

    /**
     * @param string $key
     * @return bool
     */
    public static function hasPost(string $key): bool
    {
        return isset($_POST[$key]);
    }

    /**
     * @return string
     */
    public static function getUri(): string
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
        $query = http_build_query(array_map([CharManipulation::class, 'specialCharsTrim'], $_GET));
        return $query ? $url . '?' . $query : $url;
    }

    /**
     * @return string
     */
    public static function postListString(): string
    {
        $posts = $_POST;
        $postsKeyValues = [];

        array_walk($posts, static function ($value, $key) use (&$postsKeyValues) {
            /** @var string $key For PHPStan */
            $key = CharManipulation::specialCharsTrim($key);
            /** @var string $value For PHPStan */
            $value = CharManipulation::specialCharsTrim($value);

            $postsKeyValues[] = sprintf('%s:%s', $key, $value);
        });

        return implode(',', $postsKeyValues);
    }

    /**
     * @return array<string, string>
     */
    public static function getPostList(): array
    {
        $posts = $_POST;
        $postList = [];

        array_walk($posts, static function ($value, $key) use (&$postList) {
            /** @var string $key For PHPStan */
            $key = CharManipulation::specialCharsTrim($key);
            /** @var string $value For PHPStan */
            $value = CharManipulation::specialCharsTrim($value);

            $postList[$key] = $value;
        });

        return $postList;
    }

    /**
     * @return array<string, string>
     */
    public static function getList(): array
    {
        $gets = $_GET;
        $getsList = [];

        array_walk($gets, static function ($value, $key) use (&$getsList) {
            /** @var string $key For PHPStan */
            $key = CharManipulation::specialCharsTrim($key);
            /** @var string $value For PHPStan */
            $value = CharManipulation::specialCharsTrim($value);

            $getsList[$key] = $value;
        });

        return $getsList;
    }
}
