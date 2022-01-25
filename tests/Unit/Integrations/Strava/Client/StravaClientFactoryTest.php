<?php

namespace Unit\Integrations\Strava\Client;

use App\Integrations\Strava\Client\Client\StravaClient;
use App\Integrations\Strava\Client\Client\StravaRequestHandler;
use App\Integrations\Strava\Client\StravaClientFactory;
use App\Models\User;
use GuzzleHttp\Client;
use Illuminate\Contracts\Config\Repository;
use Tests\TestCase;

class StravaClientFactoryTest extends TestCase
{

    /** @test */
    public function client_throws_an_exception_if_no_user_logged_in(){
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('No user has been given to the Strava client');

        $strava = app(StravaClientFactory::class);
        $strava->client();
    }

    /** @test */
    public function client_returns_a_client_if_user_given(){
        $user = User::factory()->create();

        $strava = app(StravaClientFactory::class);
        $client = $strava->client($user);
        $this->assertInstanceOf(StravaClient::class, $client);
        $this->assertTrue($user->is($client->getUser()));
    }

    /** @test */
    public function client_returns_a_client_if_user_not_given_and_logged_in()
    {
        $user = User::factory()->create();
        $this->be($user);

        $strava = app(StravaClientFactory::class);
        $client = $strava->client();
        $this->assertInstanceOf(StravaClient::class, $client);
        $this->assertTrue($user->is($client->getUser()));
    }

    /** @test */
    public function client_prioritises_the_given_user_over_the_logged_in_user(){
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $this->be($user1);

        $strava = app(StravaClientFactory::class);
        $client = $strava->client($user2);
        $this->assertInstanceOf(StravaClient::class, $client);
        $this->assertTrue($user2->is($client->getUser()));
    }

    /** @test */
    public function the_base_url_can_be_changed(){
        $user = User::factory()->create();
        $repo = $this->prophesize(Repository::class);
        $repo->get('services.strava.base_url')->willReturn('https://testurl.com');

        $strava = new StravaClientFactory($repo->reveal());
        $client = $strava->client($user);
        $this->assertInstanceOf(StravaClient::class, $client);
        $this->assertInstanceOf(StravaRequestHandler::class, $client->getRequestHandler());
        $this->assertInstanceOf(Client::class, $client->getRequestHandler()->getGuzzleClient());

        $reflection = new \ReflectionClass(Client::class);
        $property = $reflection->getProperty('config');
        $property->setAccessible(true);
        $value = $property->getValue($client->getRequestHandler()->getGuzzleClient())['base_uri'];

        $this->assertInstanceOf(\GuzzleHttp\Psr7\Uri::class, $value);
        $this->assertEquals('testurl.com', $value->getHost());
    }

}
