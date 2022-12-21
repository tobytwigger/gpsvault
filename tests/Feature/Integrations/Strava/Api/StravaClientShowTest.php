<?php

namespace Tests\Feature\Integrations\Strava\Api;

use App\Integrations\Strava\Client\Models\StravaClient;
use App\Models\User;
use Tests\TestCase;

class StravaClientShowTest extends TestCase
{
    /** @test */
    public function it_loads_a_strava_client()
    {
        $this->authenticatedWithSanctum();
        $this->user->givePermissionTo('manage-strava-clients');

        $ownedClient = StravaClient::factory()->create(['user_id' => $this->user->id]);

        $response = $this->getJson(route('api.integration.strava.client.show', ['client' => $ownedClient]))
            ->assertStatus(200)
            ->assertJsonFragment(['id' => $ownedClient->id]);
    }

    /** @test */
    public function it_throws_a_403_if_you_cannot_access_the_client()
    {
        $this->authenticatedWithSanctum();
        $this->user->givePermissionTo('manage-strava-clients');

        $private = StravaClient::factory()->create(['user_id' => User::factory()->create()->id, 'public' => false]);
        $public = StravaClient::factory()->create(['user_id' => User::factory()->create()->id, 'public' => true]);

        $response = $this->getJson(route('api.integration.strava.client.show', ['client' => $private]))
            ->assertStatus(403);

        $response = $this->getJson(route('api.integration.strava.client.show', ['client' => $public]))
            ->assertStatus(200)
            ->assertJsonFragment(['id' => $public->id]);
    }

    /** @test */
    public function it_returns_the_same_thing_as_the_index_for_private_clients()
    {
        $this->authenticatedWithSanctum();
        $this->user->givePermissionTo('manage-strava-clients');

        $private = StravaClient::factory()->create(['user_id' => $this->user->id, 'public' => false]);

        $response = $this->getJson(route('api.integration.strava.client.show', ['client' => $private]))
            ->assertStatus(200)
            ->json();
        $indexResponse = $this->getJson(route('api.integration.strava.client.index'))
            ->assertStatus(200)
            ->json('data.0');

        $this->assertEquals($indexResponse, $response);
    }

    /** @test */
    public function it_returns_the_same_thing_as_the_index_for_shared_clients()
    {
        $this->authenticatedWithSanctum();
        $this->user->givePermissionTo('manage-strava-clients');

        $sharedClient = StravaClient::factory()
            ->afterCreating(fn (StravaClient $client) => $client->sharedUsers()->attach($this->user->id))
            ->create();

        $response = $this->getJson(route('api.integration.strava.client.show', ['client' => $sharedClient]))
            ->assertStatus(200)
            ->json();
        $indexResponse = $this->getJson(route('api.integration.strava.client.index'))
            ->assertStatus(200)
            ->json('data.0');

        $this->assertEquals($indexResponse, $response);
    }

    /** @test */
    public function it_returns_the_same_thing_as_the_index_for_public_clients()
    {
        $this->authenticatedWithSanctum();
        $this->user->givePermissionTo('manage-strava-clients');

        $publicClient = StravaClient::factory()
            ->afterCreating(fn (StravaClient $client) => $client->sharedUsers()->attach($this->user->id))
            ->create(['public' => true]);

        $response = $this->getJson(route('api.integration.strava.client.show', ['client' => $publicClient]))
            ->assertStatus(200)
            ->json();
        $indexResponse = $this->getJson(route('api.integration.strava.client.index'))
            ->assertStatus(200)
            ->json('data.0');

        $this->assertEquals($indexResponse, $response);
    }

    /** @test */
    public function it_throws_a_403_if_no_permission_and_you_request_a_client_that_is_not_the_default_client()
    {
        $this->authenticatedWithSanctum();
        $this->user->revokePermissionTo('manage-strava-clients');

        $publicClient = StravaClient::factory()
            ->afterCreating(fn (StravaClient $client) => $client->sharedUsers()->attach($this->user->id))
            ->create(['public' => true]);

        $response = $this->getJson(route('api.integration.strava.client.show', ['client' => $publicClient]))
            ->assertStatus(403);
    }

    /** @test */
    public function it_gives_a_200_if_no_permission_and_you_request_a_client_that_is_the_default_client()
    {
        $this->authenticatedWithSanctum();
        $this->user->revokePermissionTo('manage-strava-clients');
        $this->user->givePermissionTo('manage-global-settings');

        $publicClient = StravaClient::factory()
            ->afterCreating(fn (StravaClient $client) => $client->sharedUsers()->attach($this->user->id))
            ->create(['public' => true]);
        \App\Settings\StravaClient::setValue($publicClient->id);

        $response = $this->getJson(route('api.integration.strava.client.show', ['client' => $publicClient]))
            ->assertStatus(200)
            ->assertJsonFragment(['id' => $publicClient->id]);
    }

    /** @test */
    public function you_must_be_authenticated()
    {
        $client = StravaClient::factory()->create();

        $response = $this->getJson(route('api.integration.strava.client.show', ['client' => $client]))
            ->assertUnauthorized();
    }
}
