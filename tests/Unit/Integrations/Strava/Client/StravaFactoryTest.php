<?php

namespace Unit\Integrations\Strava\Client;

use App\Integrations\Strava\Client\Client\StravaClient;
use App\Integrations\Strava\Client\StravaClientFactory;
use App\Models\User;
use Tests\TestCase;

class StravaFactoryTest extends TestCase
{

    /** @test */
    public function client_throws_an_exception_if_no_user_logged_in(){
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('No user has been given to the Strava client');

        $strava = new StravaClientFactory();
        $strava->client();
    }

    /** @test */
    public function client_returns_a_client_if_user_given(){
        $user = User::factory()->create();

        $strava = new StravaClientFactory();
        $client = $strava->client($user);
        $this->assertInstanceOf(StravaClient::class, $client);
        $this->assertTrue($user->is($client->getUser()));
    }

    /** @test */
    public function client_returns_a_client_if_user_not_given_and_logged_in()
    {
        $user = User::factory()->create();
        $this->be($user);

        $strava = new StravaClientFactory();
        $client = $strava->client();
        $this->assertInstanceOf(StravaClient::class, $client);
        $this->assertTrue($user->is($client->getUser()));
    }

    /** @test */
    public function client_prioritises_the_given_user_over_the_logged_in_user(){
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $this->be($user1);

        $strava = new StravaClientFactory();
        $client = $strava->client($user2);
        $this->assertInstanceOf(StravaClient::class, $client);
        $this->assertTrue($user2->is($client->getUser()));
    }

}
