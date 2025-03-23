<?php

namespace Uniilara\Server\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Container\BindingResolutionException;
use Uniilara\Server\Http\Server;

class ServerCommand extends Command
{
    protected $signature = 'app
                            {action=start : options (start|stop|restart|status)}
                            {--host=127.0.0.1}
                            {--port=8550}';

    protected $description = 'Start the Uniilara Workerman http server.';

    /**
     * @throws BindingResolutionException
     */
    public function handle() : void
    {
        $action = $this->argument('action');

        $host = $this->option('host');
        $port = $this->option('port');

        $validActions = ['start', 'stop', 'restart', 'reload', 'status', 'connections'];

        if (!in_array($action, $validActions)) {
            $this->error("Invalid action: '{$action}'. Available options are: " . implode(', ', $validActions));
            return;
        }

        $this->info("Starting Uniilara on http://{$host}:{$port}");


        global $argv;
        $argv[1] = $action;

        (new Server($host, $port))->run();
    }
}
