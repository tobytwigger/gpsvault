<?php

namespace App\Integrations\Strava\Client\Exceptions;

use Throwable;

class StravaRateLimitedException extends \Exception
{

    public function __construct($message = "Strava rate limit exceeded. Please try again later.", $code = 429, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

}
