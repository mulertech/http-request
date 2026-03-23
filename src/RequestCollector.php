<?php

namespace MulerTech\HttpRequest;

use Psr\Http\Message\ServerRequestInterface;

/**
 * Class RequestCollector.
 *
 * @author Sébastien Muler
 */
class RequestCollector
{
    /**
     * @var ServerRequestInterface[]
     */
    private array $requests = [];

    public function push(ServerRequestInterface $request): void
    {
        $this->requests[] = $request;
    }

    public function pop(): ?ServerRequestInterface
    {
        if (!$this->requests) {
            return null;
        }

        return array_pop($this->requests);
    }

    public function getCurrentRequest(): ?ServerRequestInterface
    {
        return end($this->requests) ?: null;
    }
}
