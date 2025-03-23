<?php

namespace Uniilara\Server\Http;

use Illuminate\Foundation\Application;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Http\Kernel as HttpKernelContract;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Illuminate\Http\Request as IlluminateRequest;

class HttpKernel
{
    protected HttpKernelContract $kernel;

    /**
     * @throws BindingResolutionException
     */
    public function __construct(Application $app)
    {
        $this->kernel = $app->make(HttpKernelContract::class);
    }

    public function handle(SymfonyRequest $symfonyRequest) : SymfonyResponse
    {
        $illuminateRequest = IlluminateRequest::createFromBase($symfonyRequest);
        return $this->kernel->handle($illuminateRequest);
    }
}