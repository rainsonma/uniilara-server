<?php

namespace Uniilara\Server;

use Workerman\Worker;
use Illuminate\Contracts\Http\Kernel;
use Workerman\Connection\TcpConnection;
use Workerman\Protocols\Http\Request as WorkermanRequest;
use Workerman\Protocols\Http\Response as WorkermanResponse;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Illuminate\Http\Request as LaravelRequest;

class HttpServer
{
    protected string $host;
    protected int $port;

    public function __construct(string $host = 'localhost', int $port = 8550)
    {
        $this->host = $host;
        $this->port = $port;
    }

    public function __invoke() : void
    {
        $app = require base_path('bootstrap/app.php');
        $kernel = $app->make(Kernel::class);

        $worker = new Worker("http://{$this->host}:{$this->port}");
        $worker->count = 4;

        $worker->onMessage = function (TcpConnection $connection, WorkermanRequest $workermanRequest) use ($kernel) {

            // Manually creating Illuminate\Http\Request does not fully initialize session, input, and headers.
            // Convert Workerman request to Symfony request
            $symfonyRequest = new SymfonyRequest(
                $workermanRequest->get(),
                $workermanRequest->post(),
                [],
                $workermanRequest->cookie(),
                $workermanRequest->file(),

                // Workerman’s server() method does not fully provide all necessary request details.
                // manually add them
                [
                    'REQUEST_URI'    => $workermanRequest->uri(),
                    'QUERY_STRING'   => $workermanRequest->queryString(),
                    'REQUEST_METHOD' => $workermanRequest->method(),
                    'CONTENT_TYPE'   => $workermanRequest->header('Content-Type', ''),
                    'REMOTE_ADDR'    => $connection->getRemoteIp(), // Get client IP
                    'HTTP_USER_AGENT' => $workermanRequest->header('User-Agent', ''),
                ],
                $workermanRequest->rawBody()
            );

            $laravelRequest = LaravelRequest::createFromBase($symfonyRequest);

            $response = $kernel->handle($laravelRequest);

            // Laravel’s response headers are in a Symfony format, so it needs to convert to Workerman.
            $workermanResponse = new WorkermanResponse(
                $response->getStatusCode(),
                iterator_to_array($response->headers->getIterator()),
                $response->getContent()
            );

            $connection->send($workermanResponse);
        };

        Worker::runAll();
    }
}
