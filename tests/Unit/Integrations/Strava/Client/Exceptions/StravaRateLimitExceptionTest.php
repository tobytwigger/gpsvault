<?php

namespace Unit\Integrations\Strava\Client\Exceptions;

use App\Integrations\Strava\Client\Exceptions\StravaRateLimited;
use Tests\TestCase;

class StravaRateLimitExceptionTest extends TestCase
{
    /** @test */
    public function it_sets_the_message_and_code()
    {
        $exception = new StravaRateLimited();
        $this->assertEquals('Strava rate limit exceeded. Please try again later.', $exception->getMessage());
        $this->assertEquals(429, $exception->getCode());
    }
}
