<?php

namespace Uniilara\Server;

use Illuminate\Support\ServiceProvider;
use Uniilara\Server\Console\Commands\ServerCommand;

class ServerServiceProvider extends ServiceProvider
{
    public function boot() : void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([ServerCommand::class]);
        }
    }
}
