<?php

namespace App\Integrations\Strava\Client\Exceptions;

use Exception;
use Throwable;

class StravaRateLimited extends Exception
{
    public function __construct($message = 'Strava rate limit exceeded. Please try again later.', $code = 429, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
