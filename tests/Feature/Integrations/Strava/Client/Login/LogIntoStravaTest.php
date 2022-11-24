<?php

namespace Feature\Integrations\Strava\Client\Login;

use App\Integrations\Strava\Client\Authentication\Authenticator;
use App\Integrations\Strava\Client\Authentication\StravaToken;
use App\Integrations\Strava\Client\Authentication\StravaTokenResponse;
use App\Integrations\Strava\Client\Models\StravaClient;
use Carbon\Carbon;
use Prophecy\Argument;
use Tests\TestCase;

class LogIntoStravaTest extends TestCase
{
    /** @test */
    public function you_must_be_authenticated()
    {
        $response = $this->get(route('strava.client.login', ['client' => 500, 'code' => 'secret-code']));
        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function it_returns_a_404_if_the_client_is_not_found()
    {
        $this->authenticated();
        $this->user->givePermissionTo('manage-strava-clients');
        $client = StravaClient::factory()->create(['user_id' => $this->user->id]);
        $client->delete();

        $response = $this->get(route('strava.client.login', ['client' => $client->id, 'code' => 'secret-code']));
        $response->assertStatus(404);
    }

    /** @test */
    public function it_returns_403_if_you_do_not_have_access_to_the_client()
    {
        $this->authenticated();
        $this->user->givePermissionTo('manage-strava-clients');
        $client = StravaClient::factory()->create();

        session()->put('stravaState', '12345');

        $response = $this->get(route('strava.client.login', ['client' => $client->id, 'code' => 'secret-code', 'state' => '12345']));
        $response->assertStatus(403);
        $response->assertSeeText('You do not have access to this client.');
    }

    /** @test */
    public function it_returns_403_if_you_are_logged_into_the_client_already()
    {
        $this->authenticated();
        $this->user->givePermissionTo('manage-strava-clients');
        $client = StravaClient::factory()->create(['user_id' => $this->user->id]);
        StravaToken::factory()->count(2)->create([
            'user_id' => $this->user->id,
            'strava_client_id' => $client->id,
        ]);
        session()->put('stravaState', '12345');

        $response = $this->get(route('strava.client.login', ['client' => $client->id, 'code' => 'secret-code', 'state' => '12345']));
        $response->assertStatus(403);
        $response->assertSeeText('You are already logged into this client.');
    }

    /** @test */
    public function it_creates_a_token_and_redirects_to_index()
    {
        $this->authenticated();
        $this->user->givePermissionTo('manage-strava-clients');
        $this->user->setAdditionalData('strava_athlete_id', 12345);
        $client = StravaClient::factory()->create(['user_id' => $this->user->id]);

        $expiresAt = Carbon::now()->addDay();
        session()->put('stravaState', '12345');

        $stravaToken = StravaTokenResponse::create(
            $expiresAt,
            500,
            'refresh-token',
            'access-token',
            12345
        );

        $authenticator = $this->prophesize(Authenticator::class);
        $authenticator->exchangeCode('secret-code', Argument::that(fn ($arg) => $arg instanceof StravaClient && $arg->is($client)))
            ->willReturn($stravaToken);
        $this->app->instance(Authenticator::class, $authenticator->reveal());
        session()->put('stravaState', '12345');

        $response = $this->get(route('strava.client.login', ['client' => $client->id, 'code' => 'secret-code', 'state' => '12345']));
        $response->assertRedirect(route('integration.strava'));

        $this->assertDatabaseHas('strava_tokens', [
            'user_id' => $this->user->id,
            'strava_client_id' => $client->id,
        ]);
        $token = StravaToken::where('user_id', $this->user->id)->where('strava_client_id', $client->id)->firstOrFail();
        $this->assertEquals('refresh-token', $token->refresh_token);
        $this->assertEquals('access-token', $token->access_token);
        $this->assertEquals($expiresAt->unix(), $token->expires_at->unix());
    }

    /** @test */
    public function it_works_for_shared_clients()
    {
        $this->authenticated();
        $this->user->givePermissionTo('manage-strava-clients');
        $this->user->setAdditionalData('strava_athlete_id', 12345);
        $client = StravaClient::factory()->create();
        $client->sharedUsers()->attach($this->user->id);

        $expiresAt = Carbon::now()->addDay();

        $stravaToken = StravaTokenResponse::create(
            $expiresAt,
            500,
            'refresh-token',
            'access-token',
            12345
        );

        $authenticator = $this->prophesize(Authenticator::class);
        $authenticator->exchangeCode('secret-code', Argument::that(fn ($arg) => $arg instanceof StravaClient && $arg->is($client)))
            ->willReturn($stravaToken);
        $this->app->instance(Authenticator::class, $authenticator->reveal());
        session()->put('stravaState', '12345');

        $response = $this->get(route('strava.client.login', ['client' => $client->id, 'code' => 'secret-code', 'state' => '12345']));
        $response->assertRedirect(route('integration.strava'));

        $this->assertDatabaseHas('strava_tokens', [
            'user_id' => $this->user->id,
            'strava_client_id' => $client->id,
        ]);
        $token = StravaToken::where('user_id', $this->user->id)->where('strava_client_id', $client->id)->firstOrFail();
        $this->assertEquals('refresh-token', $token->refresh_token);
        $this->assertEquals('access-token', $token->access_token);
        $this->assertEquals($expiresAt->unix(), $token->expires_at->unix());
    }

    /** @test */
    public function it_works_for_public_clients()
    {
        $this->authenticated();
        $this->user->givePermissionTo('manage-strava-clients');
        $this->user->setAdditionalData('strava_athlete_id', 12345);
        $client = StravaClient::factory()->create(['public' => true]);

        $expiresAt = Carbon::now()->addDay();

        $stravaToken = StravaTokenResponse::create(
            $expiresAt,
            500,
            'refresh-token',
            'access-token',
            12345
        );

        $authenticator = $this->prophesize(Authenticator::class);
        $authenticator->exchangeCode('secret-code', Argument::that(fn ($arg) => $arg instanceof StravaClient && $arg->is($client)))
            ->willReturn($stravaToken);
        $this->app->instance(Authenticator::class, $authenticator->reveal());
        session()->put('stravaState', '12345');

        $response = $this->get(route('strava.client.login', ['client' => $client->id, 'code' => 'secret-code', 'state' => '12345']));
        $response->assertRedirect(route('integration.strava'));

        $this->assertDatabaseHas('strava_tokens', [
            'user_id' => $this->user->id,
            'strava_client_id' => $client->id,
        ]);
        $token = StravaToken::where('user_id', $this->user->id)->where('strava_client_id', $client->id)->firstOrFail();
        $this->assertEquals('refresh-token', $token->refresh_token);
        $this->assertEquals('access-token', $token->access_token);
        $this->assertEquals($expiresAt->unix(), $token->expires_at->unix());
    }

    /** @test */
    public function it_validates_if_code_is_not_given()
    {
        $this->authenticated();
        $this->user->givePermissionTo('manage-strava-clients');
        $client = StravaClient::factory()->create(['user_id' => $this->user->id]);

        $response = $this->get(route('strava.client.login', ['client' => $client->id, 'state' => '12345']));
        $response->assertSessionHasErrors(['code' => 'The code field is required.']);
    }

    /** @test */
    public function todo_it_throws_an_exception_if_the_state_does_not_match()
    {
        $this->markTestIncomplete();
    }

    /** @test */
    public function todo_it_throws_an_exception_if_the_state_is_not_given()
    {
        $this->markTestIncomplete();
    }
}
