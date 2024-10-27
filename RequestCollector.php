<?php

namespace MulerTech\HttpRequest;

use Psr\Http\Message\ServerRequestInterface;

/**
 * Class RequestCollector
 * @package MulerTech\HttpRequest
 * @author Sébastien Muler
 */
class RequestCollector
{
    /**
     * @var ServerRequestInterface[]
     */
    private array $requests = [];

    /**
     * @param ServerRequestInterface $request
     */
    public function push(ServerRequestInterface $request): void
    {
        $this->requests[] = $request;
    }

    /**
     * @return ServerRequestInterface|null
     */
    public function pop(): ?ServerRequestInterface
    {
        if (!$this->requests) {
            return null;
        }
        return array_pop($this->requests);
    }

    /**
     * @return ServerRequestInterface|null
     */
    public function getCurrentRequest(): ?ServerRequestInterface
    {
        return end($this->requests) ?: null;
    }

}