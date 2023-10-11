<?php

declare(strict_types=1);

namespace App\Middleware;

use Framework\Contracts\MiddlewareInterface;
use App\Exceptions\SessionException;

class SessionMiddleware implements MiddlewareInterface
{
  public function process(callable $next)
  {
    session_set_cookie_params(
      [
        'secure' => $_ENV['APP_ENV'] === "production",
        'httponly' => true,
        'samesite' => 'lax'
      ]
    );
    if (session_start() === PHP_SESSION_ACTIVE) {
      throw new SessionException("Session already active.");
    }

    if (headers_sent($filename, $line)) {
      throw new SessionException("Header already sent. Consider enabling output buffering. Data outputted from {$filename} - Line {$line}");
    }
    session_start();

    $next();
    session_write_close();
  }
}