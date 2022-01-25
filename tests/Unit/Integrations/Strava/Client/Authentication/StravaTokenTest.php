<?php

namespace Unit\Integrations\Strava\Client\Authentication;

use App\Integrations\Strava\Client\Authentication\StravaToken;
use App\Integrations\Strava\Client\Authentication\StravaTokenResponse;
use App\Integrations\Strava\Client\Models\StravaClient;
use App\Models\User;
use Carbon\Carbon;
use Tests\TestCase;

class StravaTokenTest extends TestCase
{

    /** @test */
    public function it_belongs_to_a_client(){
        $client = StravaClient::factory()->create();
        $token = StravaToken::factory()->create(['strava_client_id' => $client->id]);

        $this->assertTrue($client->is($token->stravaClient));
    }

    /** @test */
    public function it_belongs_to_a_user(){
        $user = User::factory()->create();
        $token = StravaToken::factory()->create(['user_id' => $user->id]);

        $this->assertTrue($user->is($token->user));
    }

    /** @test */
    public function forUser_scopes_to_tokens_belonging_to_a_user(){
        $user = User::factory()->create();
        $token1 = StravaToken::factory()->create(['user_id' => $user->id]);
        $token2 = StravaToken::factory()->create();
        $token3 = StravaToken::factory()->create(['user_id' => $user->id]);

        $tokens = StravaToken::forUser($user->id)->get();
        $this->assertCount(2, $tokens);
        $this->assertTrue($token1->is($tokens[0]));
        $this->assertTrue($token3->is($tokens[1]));
    }

    /** @test */
    public function enabled_scopes_to_enabled_tokens(){
        $user = User::factory()->create();
        $token1 = StravaToken::factory()->create(['disabled' => false]);
        $token2 = StravaToken::factory()->create(['disabled' => true]);
        $token3 = StravaToken::factory()->create(['disabled' => false]);

        $tokens = StravaToken::enabled()->get();
        $this->assertCount(2, $tokens);
        $this->assertTrue($token1->is($tokens[0]));
        $this->assertTrue($token3->is($tokens[1]));
    }

    /** @test */
    public function active_scopes_to_non_expired_tokens(){
        $token1 = StravaToken::factory()->create(['expires_at' => Carbon::now()->addDay()]);
        $token2 = StravaToken::factory()->create(['expires_at' => Carbon::now()->addMinutes(3)]);
        $token3 = StravaToken::factory()->create(['expires_at' => Carbon::now()->addMinute()]); // 2 minute buffer time on expiring
        $token4 = StravaToken::factory()->create(['expires_at' => Carbon::now()->subMinute()]);

        $tokens = StravaToken::active()->get();
        $this->assertCount(2, $tokens);
        $this->assertTrue($token1->is($tokens[0]));
        $this->assertTrue($token2->is($tokens[1]));
    }

    /** @test */
    public function expired_determines_if_the_token_is_expired(){
        $token1 = StravaToken::factory()->create(['expires_at' => Carbon::now()->addDay()]);
        $token2 = StravaToken::factory()->create(['expires_at' => Carbon::now()->addMinutes(3)]);
        $token3 = StravaToken::factory()->create(['expires_at' => Carbon::now()->addMinute()]); // 2 minute buffer time on expiring
        $token4 = StravaToken::factory()->create(['expires_at' => Carbon::now()->subMinute()]);

        $this->assertFalse($token1->expired());
        $this->assertFalse($token2->expired());
        $this->assertTrue($token3->expired());
        $this->assertTrue($token4->expired());
    }

    /** @test */
    public function makeFromStravaTokenResponse_returns_a_new_strava_token_model(){
        $this->authenticated();
        $client = StravaClient::factory()->create();

        $expiresAt = Carbon::now()->addDay();
        $stravaTokenResponse = StravaTokenResponse::create(
            $expiresAt, 11, 'refresh-token', 'access-token', 5
        );

        $token = StravaToken::makeFromStravaTokenResponse($stravaTokenResponse, $client->id);
        $this->assertEquals($expiresAt->toIso8601String(), $token->expires_at->toIso8601String());
        $this->assertEquals('refresh-token', $token->refresh_token);
        $this->assertEquals('access-token', $token->access_token);
        $this->assertEquals($this->user->id, $token->user_id);
        $this->assertEquals(false, $token->disabled);
        $this->assertEquals($client->id, $token->strava_client_id);
        $this->assertFalse($token->exists);
    }

    /** @test */
    public function updateFromStravaTokenResponse_updates_a_strava_token(){
        $this->authenticated();
        $client = StravaClient::factory()->create();

        $expiresAt = Carbon::now()->addDay();
        $stravaTokenResponse = StravaTokenResponse::create(
            $expiresAt, 11, 'refresh-token-updated', 'access-token-updated', 5
        );
        $token = StravaToken::factory()->create([
            'access_token' => 'access-token',
            'refresh_token' => 'refresh-token',
            'expires_at' => $expiresAt->subHour(),
            'user_id' => $this->user->id,
            'disabled' => false,
            'strava_client_id' => $client->id
        ]);
        $token->updateFromStravaTokenResponse($stravaTokenResponse);

        $this->assertEquals($expiresAt->toIso8601String(), $token->expires_at->toIso8601String());
        $this->assertEquals('refresh-token-updated', $token->refresh_token);
        $this->assertEquals('access-token-updated', $token->access_token);
        $this->assertEquals($this->user->id, $token->user_id);
        $this->assertEquals(false, $token->disabled);
        $this->assertEquals($client->id, $token->strava_client_id);
    }

}
