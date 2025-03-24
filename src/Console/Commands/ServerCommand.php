<?php

namespace Uniilara\Server\Console\Commands;

use Illuminate\Console\Command;
use Uniilara\Server\Http\Server;
use Illuminate\Contracts\Container\BindingResolutionException;

class ServerCommand extends Command
{
    protected $signature = 'app
                            {action=start : options (start|stop|restart|status)}
                            {--host=localhost}
                            {--port=8550}
                            {--workers=4}';

    protected $description = 'Start the Uniilara Workerman http server.';

    /**
     * @throws BindingResolutionException
     */
    public function handle() : void
    {
        $action = $this->argument('action');

        $host = $this->option('host');
        $port = $this->option('port');
        $workers = $this->option('workers');

        $validActions = ['start', 'stop', 'restart', 'reload', 'status', 'connections'];

        if (!in_array($action, $validActions)) {
            $this->error("Invalid action: '{$action}'. Available options are: " . implode(', ', $validActions));
            return;
        }

        $this->info("Running action: {$action}");
        $this->info("Uniilara Workerman starts on http://{$host}:{$port}");

        global $argv;
        $argv[1] = $action;

        (new Server($host, $port, $workers))->run();
    }
}
