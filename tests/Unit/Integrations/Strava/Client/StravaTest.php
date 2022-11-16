<?php

namespace Unit\Integrations\Strava\Client;

use App\Integrations\Strava\Client\Client\StravaClient;
use App\Integrations\Strava\Client\Strava;
use App\Integrations\Strava\Client\StravaClientFactory;
use Tests\TestCase;

class StravaTest extends TestCase
{
    /** @test */
    public function it_resolves_a_factory_instance()
    {
        $this->assertInstanceOf(StravaClientFactory::class, Strava::getFacadeRoot());
    }

    /** @test */
    public function it_calls_the_underlying_instance()
    {
        $instance = $this->prophesize(StravaClientFactory::class);
        $client = $this->prophesize(StravaClient::class)->reveal();
        $instance->client()->shouldBeCalled()->willReturn($client);
        $this->app->instance(StravaClientFactory::class, $instance->reveal());

        $this->assertEquals($client, Strava::client());
    }
}
