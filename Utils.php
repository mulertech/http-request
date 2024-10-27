<?php

namespace MulerTech\HttpRequest;

use GuzzleHttp\Psr7\PumpStream;
use GuzzleHttp\Psr7\Stream;
use GuzzleHttp\Psr7\Utils as GuzzleUtils;
use Psr\Http\Message\StreamInterface;

class Utils
{
    /**
     * @param string $resource
     * @param array $options
     * @return PumpStream|Stream|StreamInterface
     */
    public static function streamFor(string $resource = '', array $options = []): PumpStream|Stream|StreamInterface
    {
        return GuzzleUtils::streamFor($resource, $options);
    }
}