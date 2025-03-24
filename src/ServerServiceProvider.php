<?php

namespace Uniilara\Server;

use Illuminate\Support\ServiceProvider;
use Uniilara\Server\Console\Commands\ServerCommand;

class ServerServiceProvider extends ServiceProvider
{
    public function register() : void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/server.php', 'server');
    }

    public function boot() : void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/server.php' => config_path('server.php'),
            ], 'uniilara-server');
            $this->commands([ServerCommand::class]);
        }
    }
}
