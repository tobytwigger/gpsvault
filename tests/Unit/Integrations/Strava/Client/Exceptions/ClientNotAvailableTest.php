<?php

namespace Unit\Integrations\Strava\Client\Exceptions;

use App\Integrations\Strava\Client\Exceptions\ClientNotAvailable;
use Tests\TestCase;

class ClientNotAvailableTest extends TestCase
{
    /** @test */
    public function it_sets_the_message_and_code()
    {
        $exception = new ClientNotAvailable();
        $this->assertEquals('Strava rate limit exceeded. Please try again later.', $exception->getMessage());
        $this->assertEquals(429, $exception->getStatusCode());
    }
}
