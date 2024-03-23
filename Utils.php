<?php

namespace MulerTech\HttpRequest;

use GuzzleHttp\Psr7\PumpStream;
use GuzzleHttp\Psr7\Stream;
use GuzzleHttp\Psr7\Utils as GuzzleUtils;
use Psr\Http\Message\StreamInterface;

class Utils
{
    /**
     * @param $resource
     * @param array $options
     * @return PumpStream|Stream|StreamInterface
     */
    public static function streamFor($resource = '', array $options = [])
    {
        return GuzzleUtils::streamFor($resource, $options);
    }
}