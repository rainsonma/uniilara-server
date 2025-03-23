<?php

namespace Uniilara\Server\Http;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\Request as LaravelRequest;
use Uniilara\Server\Support\SymfonyRequestFactory;
use Uniilara\Server\Support\WorkermanResponseFactory;
use Workerman\Connection\TcpConnection;
use Workerman\Protocols\Http\Request as WorkermanRequest;
use Workerman\Protocols\Http\Response as WorkermanResponse;
use Workerman\Worker;
use Uniilara\Server\Http\HttpKernel;

class Server
{
    protected string $host;
    protected int $port;

    public function __construct(string $host = 'localhost', int $port = 8550)
    {
        $this->host = $host;
        $this->port = $port;
    }

    /**
     * @throws BindingResolutionException
     */
    public function run() : void
    {
        $app = require base_path('bootstrap/app.php');
        $kernel = new HttpKernel($app);

        $worker = new Worker("http://{$this->host}:{$this->port}");
        $worker->count = 4;

        $worker->onMessage = function (TcpConnection $connection, WorkermanRequest $workermanRequest) use ($kernel) {

            // Manually creating Illuminate\Http\Request does not fully initialize session, input, and headers.
            // Convert Workerman request to Symfony request
            $symfonyRequest = SymfonyRequestFactory::fromWorkerman($connection, $workermanRequest);
            $response = $kernel->handle($symfonyRequest);

            // Laravel’s response headers are in a Symfony format, so it needs to convert to Workerman.
            $workermanResponse = WorkermanResponseFactory::fromLaravel($response);

            $connection->send($workermanResponse);
        };

        Worker::runAll();
    }
}
