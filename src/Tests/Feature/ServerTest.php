<?php

namespace Uniilara\Server\Tests\Feature;

use Orchestra\Testbench\TestCase;
use Uniilara\Server\ServerServiceProvider;

class ServerTest extends TestCase
{
    protected function getPackageProviders($app) : array
    {
        return [ServerServiceProvider::class];
    }

    public function test_command_is_registered() : void
    {
        $this->artisan('app start')
            ->expectsOutput('Running action: start')
            ->expectsOutput('Uniilara Workerman starts on http://127.0.0.1:8550')
            ->assertExitCode(0);
    }
}