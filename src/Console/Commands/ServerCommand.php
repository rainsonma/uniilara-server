<?php

namespace Uniilara\Server\Console\Commands;

use Uniilara\Server\HttpServer;
use Illuminate\Console\Command;

class ServerCommand extends Command
{
    protected $signature = 'app
                            {action=start : options (start|stop|restart|status)}
                            {--host=127.0.0.1}
                            {--port=8550}';

    protected $description = 'Start the Uniilara Workerman http server.';

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

        (new HttpServer($host, $port))();
    }
}
