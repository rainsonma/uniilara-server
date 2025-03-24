<?php

namespace Uniilara\Server\Http;

use Workerman\Worker;
use Workerman\Connection\TcpConnection;
use Uniilara\Server\Support\SymfonyRequestFactory;
use Uniilara\Server\Support\WorkermanResponseFactory;
use Workerman\Protocols\Http\Request as WorkermanRequest;
use Illuminate\Contracts\Container\BindingResolutionException;

class Server
{
    protected string $host;
    protected int $port;
    protected int $workers;

    public function __construct(
        string $host = "localhost",
        int $port = 8550,
        int $workers = 4
    )
    {
        $this->host = $host;
        $this->port = $port;
        $this->workers = $workers;
    }

    /**
     * @throws BindingResolutionException
     */
    public function run() : void
    {
        if (app()->environment('testing')) {
            echo "Workerman boot skipped in test.\n";
            return;
        }

        $app = require base_path('bootstrap/app.php');
        $kernel = new HttpKernel($app);

        $worker = new Worker("http://{$this->host}:{$this->port}");
        $worker->count = $this->workers;

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
