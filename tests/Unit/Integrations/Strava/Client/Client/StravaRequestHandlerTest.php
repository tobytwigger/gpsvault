<?php

namespace Unit\Integrations\Strava\Client\Client;

use App\Integrations\Strava\Client\Authentication\Authenticator;
use App\Integrations\Strava\Client\Authentication\StravaToken;
use App\Integrations\Strava\Client\Client\StravaRequestHandler;
use App\Integrations\Strava\Client\Exceptions\ClientNotAvailable;
use App\Integrations\Strava\Client\Models\StravaClient;
use App\Models\User;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Tests\TestCase;

class StravaRequestHandlerTest extends TestCase
{

    /** @test */
    public function unauthenticated_request_makes_a_request()
    {
        $this->markTestSkipped();
        $user = User::factory()->create();

        $response = new Response(200, [], json_encode(['my' => 'test']));
        $guzzleClient = $this->prophesize(Client::class);
        $guzzleClient->request('get', 'test', [])
            ->shouldBeCalled()
            ->willReturn($response);
        $authenticator = new Authenticator($user, $guzzleClient->reveal());

        $handler = new StravaRequestHandler(
            $authenticator,
            $user,
            $guzzleClient->reveal()
        );

        $newResponse = $handler->unauthenticatedRequest('get', 'test', []);
        $this->assertEquals($response, $newResponse);
    }

    /** @test */
    public function request_makes_a_request_with_an_available_client()
    {
        $this->markTestSkipped();
        $user = User::factory()->create();
        $user->givePermissionTo('manage-strava-clients');

        $client = StravaClient::factory()->full()->create(['user_id' => $user->id]);
        $client2 = StravaClient::factory()->create(['user_id' => $user->id]);
        $token = StravaToken::factory()->create(['user_id' => $user->id, 'strava_client_id' => $client2->id]);

        $response = new Response(200, [], json_encode(['my' => 'test']));
        $guzzleClient = $this->prophesize(Client::class);
        $guzzleClient->request('get', 'test', ['headers' => ['Authorization' => 'Bearer ' . $token->access_token]])
            ->shouldBeCalled()
            ->willReturn($response);
        $authenticator = new Authenticator($user, $guzzleClient->reveal());

        $handler = new StravaRequestHandler(
            $authenticator,
            $user,
            $guzzleClient->reveal()
        );

        $response = $handler->request('get', 'test', []);
    }

    /** @test */
    public function request_updates_rate_limits_on_successful_response()
    {
        $this->markTestSkipped();
        $user = User::factory()->create();
        $user->givePermissionTo('manage-strava-clients');

        $client = StravaClient::factory()->full()->create(['user_id' => $user->id]);
        $client2 = StravaClient::factory()->create(['user_id' => $user->id]);
        $token = StravaToken::factory()->create(['user_id' => $user->id, 'strava_client_id' => $client2->id]);

        $response = new Response(200, [
            'X-RateLimit-Usage' => '50,148',
            'X-RateLimit-Limit' => '101,1001',
        ], json_encode(['my' => 'test']));
        $guzzleClient = $this->prophesize(Client::class);
        $guzzleClient->request('get', 'test', ['headers' => ['Authorization' => 'Bearer ' . $token->access_token]])
            ->shouldBeCalled()
            ->willReturn($response);
        $authenticator = new Authenticator($user, $guzzleClient->reveal());

        $handler = new StravaRequestHandler(
            $authenticator,
            $user,
            $guzzleClient->reveal()
        );

        $response = $handler->request('get', 'test', []);

        $this->assertDatabaseHas('strava_clients', [
            'id' => $client2->id,
            'limit_15_min' => 101,
            'used_15_min_calls' => 50,
            'limit_daily' => 1001,
            'used_daily_calls' => 148
        ]);
    }

    /** @test */
    public function request_updates_rate_limits_on_429_exception()
    {
        $this->markTestSkipped();
        $this->expectException(ClientNotAvailable::class);

        $user = User::factory()->create();
        $user->givePermissionTo('manage-strava-clients');

        $client = StravaClient::factory()->full()->create(['user_id' => $user->id]);
        $client2 = StravaClient::factory()->create(['user_id' => $user->id]);
        $token = StravaToken::factory()->create(['user_id' => $user->id, 'strava_client_id' => $client2->id]);
        \App\Settings\StravaClient::setValue($client2->id);

        $guzzleClient = $this->prophesize(Client::class);
        $request = $this->prophesize(Request::class)->reveal();

        $guzzleClient->request('get', 'test', ['headers' => ['Authorization' => 'Bearer ' . $token->access_token]])
            ->shouldBeCalled()
            ->willThrow(new ClientException('Rate limit', $request, new Response(
                429,
                [
                    'X-RateLimit-Usage' => '50,148',
                    'X-RateLimit-Limit' => '101,1001',
                ],
                json_encode(['my' => 'test'])
            )));
        $authenticator = new Authenticator($user, $guzzleClient->reveal());

        $handler = new StravaRequestHandler(
            $authenticator,
            $user,
            $guzzleClient->reveal()
        );

        $response = $handler->request('get', 'test', []);

        $this->assertDatabaseHas('strava_clients', [
            'id' => $client2->id,
            'limit_15_min' => 101,
            'used_15_min_calls' => 50,
            'limit_daily' => 1001,
            'used_daily_calls' => 148
        ]);
    }

    /** @test */
    public function request_throws_an_exception_that_isnt_a_429_and_updates_rate_limits()
    {
        $this->markTestSkipped();
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Something is wrong.');

        $user = User::factory()->create();
        $user->givePermissionTo('manage-strava-clients');

        $client = StravaClient::factory()->full()->create(['user_id' => $user->id]);
        $client2 = StravaClient::factory()->create(['user_id' => $user->id]);
        $token = StravaToken::factory()->create(['user_id' => $user->id, 'strava_client_id' => $client2->id]);
        \App\Settings\StravaClient::setValue($client2->id);

        $guzzleClient = $this->prophesize(Client::class);
        $request = $this->prophesize(Request::class)->reveal();

        $guzzleClient->request('get', 'test', ['headers' => ['Authorization' => 'Bearer ' . $token->access_token]])
            ->shouldBeCalled()
            ->willThrow(new ClientException('Something is wrong.', $request, new Response(
                422,
                [
                    'X-RateLimit-Usage' => '50,148',
                    'X-RateLimit-Limit' => '101,1001',
                ],
                json_encode(['my' => 'test'])
            )));
        $authenticator = new Authenticator($user, $guzzleClient->reveal());

        $handler = new StravaRequestHandler(
            $authenticator,
            $user,
            $guzzleClient->reveal()
        );

        $response = $handler->request('get', 'test', []);

        $this->assertDatabaseHas('strava_clients', [
            'id' => $client2->id,
            'limit_15_min' => 101,
            'used_15_min_calls' => 50,
            'limit_daily' => 1001,
            'used_daily_calls' => 148
        ]);
    }

    /** @test */
    public function request_tries_with_other_available_clients_if_first_client_throws_rate_limit_exception()
    {
        $this->markTestSkipped();
        $user = User::factory()->create();
        $user->givePermissionTo('manage-strava-clients');

        $client = StravaClient::factory()->full()->create(['user_id' => $user->id]);
        $client3 = StravaClient::factory()->create(['user_id' => $user->id]);
        $token3 = StravaToken::factory()->create(['user_id' => $user->id, 'strava_client_id' => $client3->id]);
        $client2 = StravaClient::factory()->create(['user_id' => $user->id]);
        $token2 = StravaToken::factory()->create(['user_id' => $user->id, 'strava_client_id' => $client2->id]);
        \App\Settings\StravaClient::setValue($client2->id);

        $guzzleClient = $this->prophesize(Client::class);
        $request = $this->prophesize(Request::class)->reveal();

        $guzzleClient->request('get', 'test', ['headers' => ['Authorization' => 'Bearer ' . $token3->access_token]])
            ->shouldBeCalled()
            ->willThrow(new ClientException('Rate limit', $request, new Response(
                429,
                [
                    'X-RateLimit-Usage' => '501,1481',
                    'X-RateLimit-Limit' => '1011,10011',
                ],
                json_encode(['my' => 'test'])
            )));

        $guzzleClient->request('get', 'test', ['headers' => ['Authorization' => 'Bearer ' . $token2->access_token]])
            ->shouldBeCalled()
            ->willReturn(new Response(200, [
                'X-RateLimit-Usage' => '50,148',
                'X-RateLimit-Limit' => '101,1001',
            ], json_encode(['my' => 'test'])));
        $authenticator = new Authenticator($user, $guzzleClient->reveal());

        $handler = new StravaRequestHandler(
            $authenticator,
            $user,
            $guzzleClient->reveal()
        );

        $response = $handler->request('get', 'test', []);

        $this->assertDatabaseHas('strava_clients', [
            'id' => $client2->id,
            'limit_15_min' => 101,
            'used_15_min_calls' => 50,
            'limit_daily' => 1001,
            'used_daily_calls' => 148
        ]);
        $this->assertDatabaseHas('strava_clients', [
            'id' => $client3->id,
            'limit_15_min' => 1011,
            'used_15_min_calls' => 501,
            'limit_daily' => 10011,
            'used_daily_calls' => 1481
        ]);
    }

    /** @test */
    public function request_throws_an_exception_if_all_clients_are_full_or_throw_rate_limit_exception()
    {
        $this->markTestSkipped();
        $this->expectException(ClientNotAvailable::class);

        $user = User::factory()->create();
        $user->givePermissionTo('manage-strava-clients');

        $client = StravaClient::factory()->full()->create(['user_id' => $user->id]);
        $client3 = StravaClient::factory()->create(['user_id' => $user->id]);
        $token3 = StravaToken::factory()->create(['user_id' => $user->id, 'strava_client_id' => $client3->id]);
        $client2 = StravaClient::factory()->create(['user_id' => $user->id]);
        $token2 = StravaToken::factory()->create(['user_id' => $user->id, 'strava_client_id' => $client2->id]);
        \App\Settings\StravaClient::setValue($client2->id);

        $guzzleClient = $this->prophesize(Client::class);
        $request = $this->prophesize(Request::class)->reveal();

        $guzzleClient->request('get', 'test', ['headers' => ['Authorization' => 'Bearer ' . $token3->access_token]])
            ->shouldBeCalled()
            ->willThrow(new ClientException('Rate limit', $request, new Response(
                429,
                [
                    'X-RateLimit-Usage' => '501,1481',
                    'X-RateLimit-Limit' => '1011,10011',
                ],
                json_encode(['my' => 'test'])
            )));

        $guzzleClient->request('get', 'test', ['headers' => ['Authorization' => 'Bearer ' . $token2->access_token]])
            ->shouldBeCalled()
            ->willThrow(new ClientException('Rate limit', $request, new Response(
                429,
                [
                    'X-RateLimit-Usage' => '502,1482',
                    'X-RateLimit-Limit' => '1012,10012',
                ],
                json_encode(['my' => 'test'])
            )));
        $authenticator = new Authenticator($user, $guzzleClient->reveal());

        $handler = new StravaRequestHandler(
            $authenticator,
            $user,
            $guzzleClient->reveal()
        );

        $response = $handler->request('get', 'test', []);

        $this->assertDatabaseHas('strava_clients', [
            'id' => $client2->id,
            'limit_15_min' => 1012,
            'used_15_min_calls' => 502,
            'limit_daily' => 10012,
            'used_daily_calls' => 1482
        ]);
        $this->assertDatabaseHas('strava_clients', [
            'id' => $client3->id,
            'limit_15_min' => 1011,
            'used_15_min_calls' => 501,
            'limit_daily' => 10011,
            'used_daily_calls' => 1481
        ]);
    }

    /** @test */
    public function decode_response_decodes_a_response()
    {
        $this->markTestSkipped();
        $user = User::factory()->create();
        $guzzleClient = $this->prophesize(Client::class);
        $authenticator = new Authenticator($user, $guzzleClient->reveal());

        $handler = new StravaRequestHandler(
            $authenticator,
            $user,
            $guzzleClient->reveal()
        );

        $response = new Response(200, [
            'X-RateLimit-Usage' => '50,148',
            'X-RateLimit-Limit' => '101,1001',
        ], json_encode(['my' => 'test']));
        $result = $handler->decodeResponse($response);
        $this->assertEquals(['my' => 'test'], $result);
    }

    /** @test */
    public function get_guzzle_client_returns_the_guzzle_client()
    {
        $this->markTestSkipped();
        $user = User::factory()->create();
        $guzzleClient = $this->prophesize(Client::class);
        $authenticator = new Authenticator($user, $guzzleClient->reveal());

        $handler = new StravaRequestHandler(
            $authenticator,
            $user,
            $guzzleClient->reveal()
        );

        $client = $handler->getGuzzleClient();
        $this->assertInstanceOf(Client::class, $client);
        $this->assertEquals($guzzleClient->reveal(), $client);
    }
}
