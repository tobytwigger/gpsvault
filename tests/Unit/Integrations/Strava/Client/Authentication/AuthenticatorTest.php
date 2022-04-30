<?php

namespace Unit\Integrations\Strava\Client\Authentication;

use App\Integrations\Strava\Client\Authentication\Authenticator;
use App\Integrations\Strava\Client\Authentication\StravaToken;
use App\Integrations\Strava\Client\Authentication\StravaTokenResponse;
use App\Integrations\Strava\Client\Models\StravaClient;
use App\Models\User;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Illuminate\Validation\UnauthorizedException;
use Prophecy\Argument;
use Tests\TestCase;

class AuthenticatorTest extends TestCase
{

    /** @test */
    public function it_throws_an_exception_if_no_tokens_are_found_for_the_client()
    {
        $this->expectException(UnauthorizedException::class);
        $this->expectExceptionMessage('Your account is not connected to Strava.');

        $client = StravaClient::factory()->create();
        $user = User::factory()->create();

        $guzzleClient = $this->prophesize(Client::class);

        $authenticator = new Authenticator($user, $guzzleClient->reveal());
        $authenticator->getAuthToken($client);
    }

    /** @test */
    public function it_throws_an_exception_when_only_disabled_tokens_are_available()
    {
        $this->expectException(UnauthorizedException::class);
        $this->expectExceptionMessage('Your account is not connected to Strava.');

        $client = StravaClient::factory()->create();
        $user = User::factory()->create();
        StravaToken::factory()->create(['strava_client_id' => $client->id, 'disabled' => true, 'user_id' => $user->id]);

        $guzzleClient = $this->prophesize(Client::class);

        $authenticator = new Authenticator($user, $guzzleClient->reveal());
        $authenticator->getAuthToken($client);
    }

    /** @test */
    public function it_scopes_tokens_to_the_current_user()
    {
        $this->expectException(UnauthorizedException::class);
        $this->expectExceptionMessage('Your account is not connected to Strava.');

        $client = StravaClient::factory()->create();
        $user = User::factory()->create();
        StravaToken::factory()->create(['strava_client_id' => $client->id, 'user_id' => User::factory()->create()->id]);

        $guzzleClient = $this->prophesize(Client::class);

        $authenticator = new Authenticator($user, $guzzleClient->reveal());
        $authenticator->getAuthToken($client);
    }

    /** @test */
    public function it_returns_the_access_token_for_a_token_that_has_not_expired()
    {
        $client = StravaClient::factory()->create();
        $user = User::factory()->create();
        StravaToken::factory()->create(['strava_client_id' => $client->id, 'access_token' => 'my-token', 'user_id' => $user->id]);

        $guzzleClient = $this->prophesize(Client::class);

        $authenticator = new Authenticator($user, $guzzleClient->reveal());
        $this->assertEquals('my-token', $authenticator->getAuthToken($client));
    }

    /** @test */
    public function it_refreshes_an_access_token_when_it_has_expired()
    {
        $client = StravaClient::factory()->create(['client_id' => 'my-client-id', 'client_secret' => 'my-client-secret']);
        $user = User::factory()->create();
        $user->setAdditionalData('strava_athlete_id', 12345);

        $token = StravaToken::factory()->create([
            'strava_client_id' => $client->id, 'access_token' => 'my-token', 'user_id' => $user->id, 'refresh_token' => 'my-refresh-token', 'expires_at' => Carbon::now()->subDay(), ]);
        $expiresAt = Carbon::now()->addDay();

        $guzzleClient = $this->prophesize(Client::class);
        $guzzleClient->request('post', 'https://www.strava.com/oauth/token', [
            'query' => [
                'client_id' => 'my-client-id', 'client_secret' => 'my-client-secret', 'refresh_token' => 'my-refresh-token', 'grant_type' => 'refresh_token',
            ],
        ])->shouldBeCalled()->willReturn(new Response(200, [], json_encode([
            'expires_at' => $expiresAt->unix(), 'expires_in' => 500, 'refresh_token' => 'new-refresh-token', 'access_token' => 'new-access-token',
        ])));
        $authenticator = new Authenticator($user, $guzzleClient->reveal());
        $this->assertEquals('new-access-token', $authenticator->getAuthToken($client));

        $token->refresh();
        $this->assertEquals('new-access-token', $token->access_token);
        $this->assertEquals('new-refresh-token', $token->refresh_token);
        $this->assertEquals($expiresAt->toIso8601String(), $token->expires_at->toIso8601String());
    }

    /** @test */
    public function it_throws_an_exception_if_athlete_id_is_not_set()
    {
        $user = User::factory()->create();

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Athlete ID not set for user ' . $user->id . '.');

        $client = StravaClient::factory()->create();
        $token = StravaToken::factory()->create(['strava_client_id' => $client->id, 'user_id' => $user->id, 'expires_at' => Carbon::now()->subDay()]);

        $guzzleClient = $this->prophesize(Client::class);
        $guzzleClient->request('post', 'https://www.strava.com/oauth/token', Argument::type('array'))->shouldBeCalled()->willReturn(new Response(200, [], json_encode([
            'expires_at' => Carbon::now()->addDay()->unix(), 'expires_in' => 500, 'refresh_token' => 'new-refresh-token', 'access_token' => 'new-access-token',
        ])));
        $authenticator = new Authenticator($user, $guzzleClient->reveal());
        $authenticator->getAuthToken($client);
    }

    /** @test */
    public function exchange_code_creates_a_strava_token_representation()
    {
        $client = StravaClient::factory()->create(['client_id' => 'my-client-id', 'client_secret' => 'my-client-secret']);
        $user = User::factory()->create();
        $user->setAdditionalData('strava_athlete_id', 12345);

        $expiresAt = Carbon::now()->addDay();

        $guzzleClient = $this->prophesize(Client::class);
        $guzzleClient->request('post', 'https://www.strava.com/oauth/token', [
            'query' => [
                'client_id' => $client->client_id,
                'client_secret' => $client->client_secret,
                'code' => '12345-code',
                'grant_type' => 'authorization_code',
            ],
        ])->shouldBeCalled()->willReturn(new Response(200, [], json_encode([
            'expires_at' => $expiresAt->unix(),
            'expires_in' => 500,
            'refresh_token' => 'new-refresh-token',
            'access_token' => 'new-access-token',
            'athlete' => ['id' => 12345],
        ])));

        $authenticator = new Authenticator($user, $guzzleClient->reveal());
        $newToken = $authenticator->exchangeCode('12345-code', $client);
        $this->assertInstanceOf(StravaTokenResponse::class, $newToken);

        $this->assertEquals(12345, $newToken->getAthleteId());
        $this->assertEquals(500, $newToken->getExpiresIn());
        $this->assertEquals('new-access-token', $newToken->getAccessToken());
        $this->assertEquals('new-refresh-token', $newToken->getRefreshToken());
        $this->assertEquals($expiresAt->unix(), Carbon::make($newToken->getExpiresAt())->unix());
    }
}
