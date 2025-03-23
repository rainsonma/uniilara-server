<?php

namespace Uniilara\Server\Support;

use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Workerman\Protocols\Http\Response as WorkermanResponse;

class WorkermanResponseFactory
{
    public static function fromLaravel(SymfonyResponse $response) : WorkermanResponse
    {
        return new WorkermanResponse(
            $response->getStatusCode(),
            iterator_to_array($response->headers->getIterator()),
            $response->getContent()
        );
    }
}