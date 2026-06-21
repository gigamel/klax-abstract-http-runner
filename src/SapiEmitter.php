<?php

declare(strict_types=1);

namespace Klax\Http\Skeleton\Runner;

use Klax\Http\Runner\Contract\SapiEmitterInterface;
use Psr\Http\Message\ResponseInterface;

readonly class SapiEmitter implements SapiEmitterInterface
{
    public function __construct(
        protected int $bufferSize = 8192,
    ) {
    }

    public function emit(ResponseInterface $response): void
    {
        $this->emitStatusLine($response);
        $this->emitHeaders($response);
        $this->emitBody($response);
    }

    protected function emitHeaders(ResponseInterface $response): void
    {
        $statusCode = $response->getStatusCode();

        foreach ($response->getHeaders() as $key => $values) {
            $header = ucwords($key, '-');

            $replace = ($header !== 'Set-Cookie');

            foreach ($values as $value) {
                header(
                    sprintf(
                        '%s: %s',
                        $header,
                        $value,
                    ),
                    $replace,
                    $statusCode,
                );

                $replace = false;
            }
        }
    }

    protected function emitStatusLine(ResponseInterface $response): void
    {
        $reasonPhrase = $response->getReasonPhrase();

        header(
            sprintf(
                'HTTP/%s %d%s',
                $response->getProtocolVersion(),
                $response->getStatusCode(),
                $reasonPhrase !== '' ? ' ' . $reasonPhrase : '',
            ),
            true,
            $response->getStatusCode(),
        );
    }

    protected function emitBody(ResponseInterface $response): void
    {
        $body = $response->getBody();

        if ($body->isSeekable()) {
            $body->rewind();
        }

        while (!$body->eof()) {
            echo $body->read($this->bufferSize);
        }
    }
}
