<?php

namespace Uniilara\Server\Support;

use Workerman\Connection\TcpConnection;
use Workerman\Protocols\Http\Request as WorkermanRequest;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

class SymfonyRequestFactory
{
    public static function fromWorkerman(TcpConnection $connection, WorkermanRequest $request) : SymfonyRequest
    {
        // Manually creating Illuminate\Http\Request does not fully initialize session, input, and headers.
        // Convert Workerman request to Symfony request
        return new SymfonyRequest(
            $request->get(),
            $request->post(),
            [],
            $request->cookie(),
            $request->file(),

            // Workerman’s server() method does not fully provide all necessary request details.
            // manually add them
            [
                'REQUEST_URI'     => $request->uri(),
                'QUERY_STRING'    => $request->queryString(),
                'REQUEST_METHOD'  => $request->method(),
                'CONTENT_TYPE'    => $request->header('Content-Type', ''),
                'REMOTE_ADDR'     => $connection->getRemoteIp(),
                'HTTP_USER_AGENT' => $request->header('User-Agent', ''),
            ],
            $request->rawBody()
        );
    }
}