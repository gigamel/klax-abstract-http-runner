<?php

declare(strict_types=1);

namespace Klax\Http\Skeleton\Runner;

use Klax\Http\Runner\Contract\EmergencyThrowableHandlerInterface;
use Klax\Http\Runner\Contract\HttpRunnerInterface;
use Klax\Http\Runner\Contract\MainRequestHandlerInterface;
use Klax\Http\Runner\Contract\SapiEmitterInterface;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;

abstract readonly class AbstractHttpRunner implements HttpRunnerInterface
{
    public function __construct(
        protected MainRequestHandlerInterface $mainRequestHandler,
        protected SapiEmitterInterface $emitter,
        protected EmergencyThrowableHandlerInterface $emergencyThrowableHandler,
    ) {
    }

    public function run(ServerRequestInterface $request): void
    {
        try {
            $response = $this->mainRequestHandler->handle($request);
            $this->emitter->emit($response);
        } catch (Throwable $emergencyThrowable) {
            $this->emergencyThrowableHandler->handle($request, $emergencyThrowable);
        }
    }
}
