<?php

namespace App\Integrations\Strava\Client\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class ClientNotAvailable extends HttpException
{
    public function __construct($message = 'Strava rate limit exceeded. Please try again later.', $code = 429, ?Throwable $previous = null)
    {
        parent::__construct($code, $message, $previous);
    }
}
