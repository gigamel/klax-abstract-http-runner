<?php

declare(strict_types=1);

namespace Klax\Http\Skeleton\Runner;

use Klax\Http\Runner\Contract\EmergencyThrowableHandlerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;

readonly class EmergencyThrowableHandler implements EmergencyThrowableHandlerInterface
{
    public function handle(ServerRequestInterface $request, Throwable $throwable): void
    {
        error_log((string)$throwable);

        if (ob_get_level() > 0) {
            ob_end_clean();
        }

        if (!headers_sent()) {
            header("HTTP/1.1 500 Internal Server Error", true, 500);
            header("Content-Type: text/html; charset=utf-8");
        }

        echo <<<EOF
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"/>
<title>Internal Server Error</title>
</head>
<body>
<h1>Oops! It seems server error</h1>
<p>Sorry, there was a mistake. We are already carrying out work to eliminate.</p>
</body>
</html>
EOF;
    }
}
