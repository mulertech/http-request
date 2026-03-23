<?php

namespace MulerTech\HttpRequest;

use MulerTech\CharManipulation\CharManipulation;

/**
 * Class HttpRequest.
 *
 * @author Sébastien Muler
 */
class HttpRequest
{
    public static function getCookie(string $key): ?string
    {
        if (!isset($_COOKIE[$key])) {
            return null;
        }

        $raw = $_COOKIE[$key];
        $result = CharManipulation::specialCharsTrim(is_string($raw) || is_array($raw) ? $raw : null);

        return is_string($result) ? $result : null;
    }

    public static function hasCookie(string $key): bool
    {
        return isset($_COOKIE[$key]);
    }

    public static function get(string $key): ?string
    {
        if (!isset($_GET[$key])) {
            return null;
        }

        $raw = $_GET[$key];
        $result = CharManipulation::specialCharsTrim(is_string($raw) || is_array($raw) ? $raw : null);

        return is_string($result) ? $result : null;
    }

    public static function has(string $key): bool
    {
        return isset($_GET[$key]);
    }

    public static function method(): string
    {
        $method = $_SERVER['REQUEST_METHOD'];

        return is_string($method) ? $method : '';
    }

    /**
     * @return string|array<int|string, mixed>|null
     */
    public static function getPost(string $key): string|array|null
    {
        if (!isset($_POST[$key])) {
            return null;
        }

        $raw = $_POST[$key];

        return CharManipulation::specialCharsTrim(is_string($raw) || is_array($raw) ? $raw : null);
    }

    public static function hasPost(string $key): bool
    {
        return isset($_POST[$key]);
    }

    public static function getUri(): string
    {
        $uri = $_SERVER['REQUEST_URI'];

        return is_string($uri) ? $uri : '';
    }

    /**
     * get url with get options.
     */
    public static function getUrl(): string
    {
        $phpSelf = $_SERVER['PHP_SELF'];
        $url = is_string($phpSelf) ? $phpSelf : '';
        $query = http_build_query(array_map(
            static fn (mixed $v): string|array|null => CharManipulation::specialCharsTrim(
                is_string($v) || is_array($v) ? $v : null
            ),
            $_GET
        ));

        return $query ? $url.'?'.$query : $url;
    }

    public static function postListString(): string
    {
        $posts = $_POST;
        $postsKeyValues = [];

        array_walk($posts, static function (mixed $value, string $key) use (&$postsKeyValues) {
            $trimmedKey = CharManipulation::specialCharsTrim($key);
            $trimmedValue = CharManipulation::specialCharsTrim(is_string($value) || is_array($value) ? $value : null);

            $postsKeyValues[] = sprintf('%s:%s', is_string($trimmedKey) ? $trimmedKey : '', is_string($trimmedValue) ? $trimmedValue : '');
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

        array_walk($posts, static function (mixed $value, string $key) use (&$postList) {
            $trimmedKey = CharManipulation::specialCharsTrim($key);
            $trimmedValue = CharManipulation::specialCharsTrim(is_string($value) || is_array($value) ? $value : null);

            $postList[is_string($trimmedKey) ? $trimmedKey : ''] = is_string($trimmedValue) ? $trimmedValue : '';
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

        array_walk($gets, static function (mixed $value, string $key) use (&$getsList) {
            $trimmedKey = CharManipulation::specialCharsTrim($key);
            $trimmedValue = CharManipulation::specialCharsTrim(is_string($value) || is_array($value) ? $value : null);

            $getsList[is_string($trimmedKey) ? $trimmedKey : ''] = is_string($trimmedValue) ? $trimmedValue : '';
        });

        return $getsList;
    }
}
