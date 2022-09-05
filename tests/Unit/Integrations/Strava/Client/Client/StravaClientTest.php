<?php

namespace Unit\Integrations\Strava\Client\Client;

use App\Integrations\Strava\Client\Client\Resources\Activity;
use App\Integrations\Strava\Client\Client\Resources\Webhook;
use App\Integrations\Strava\Client\Client\StravaClient;
use App\Integrations\Strava\Client\Client\StravaRequestHandler;
use App\Models\User;
use Tests\TestCase;

class StravaClientTest extends TestCase
{
    /** @test */
    public function it_creates_an_activity_handler()
    {
        $user = User::factory()->create();
        $requestHandler = $this->prophesize(StravaRequestHandler::class);

        $client = new StravaClient($user, $requestHandler->reveal());
        $handler = $client->activity();

        $this->assertInstanceOf(Activity::class, $handler);
        $this->assertTrue($user->is($handler->user));
        $this->assertEquals($requestHandler->reveal(), $handler->request);
    }

    public function it_creates_a_webhook_handler()
    {
        $user = User::factory()->create();
        $requestHandler = $this->prophesize(StravaRequestHandler::class);

        $client = new StravaClient($user, $requestHandler->reveal());
        $handler = $client->webhook();

        $this->assertInstanceOf(Webhook::class, $handler);
        $this->assertTrue($user->is($handler->user));
        $this->assertEquals($requestHandler->reveal(), $handler->request);
    }

    /** @test */
    public function the_user_can_be_retrieved()
    {
        $user = User::factory()->create();
        $requestHandler = $this->prophesize(StravaRequestHandler::class);

        $client = new StravaClient($user, $requestHandler->reveal());

        $this->assertInstanceOf(User::class, $client->getUser());
        $this->assertTrue($user->is($client->getUser()));
    }

    /** @test */
    public function the_request_handler_can_be_retrieved()
    {
        $user = User::factory()->create();
        $requestHandler = $this->prophesize(StravaRequestHandler::class);

        $client = new StravaClient($user, $requestHandler->reveal());

        $this->assertInstanceOf(StravaRequestHandler::class, $client->getRequestHandler());
        $this->assertEquals($requestHandler->reveal(), $client->getRequestHandler());
    }
}
