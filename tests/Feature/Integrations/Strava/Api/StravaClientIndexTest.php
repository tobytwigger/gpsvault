<?php

namespace Tests\Feature\Integrations\Strava\Api;

use App\Integrations\Strava\Client\Authentication\StravaToken;
use App\Integrations\Strava\Client\Exceptions\ClientNotAvailable;
use App\Integrations\Strava\Client\Models\StravaClient;
use App\Models\User;
use Tests\TestCase;

class StravaClientIndexTest extends TestCase
{
    /** @test */
    public function index_loads_strava_clients()
    {
        $this->authenticatedWithSanctum();
        $this->user->givePermissionTo('manage-strava-clients');

        $ownedClients = StravaClient::factory()->count(5)->create(['user_id' => $this->user->id]);
        $sharedClients = StravaClient::factory()->count(4)
            ->afterCreating(fn (StravaClient $client) => $client->sharedUsers()->attach($this->user->id))
            ->create();
        $publicClients = StravaClient::factory()->count(3)
            ->afterCreating(fn (StravaClient $client) => $client->sharedUsers()->attach($this->user->id))
            ->create(['public' => true]);

        $response = $this->getJson(route('api.integration.strava.client.index', ['perPage' => 1000, 'page' => 1]))
            ->assertStatus(200)
            ->assertJsonCount(12, 'data')
            ->assertJsonPath('data.0.id', $ownedClients[0]->id)
            ->assertJsonPath('data.1.id', $ownedClients[1]->id)
            ->assertJsonPath('data.2.id', $ownedClients[2]->id)
            ->assertJsonPath('data.3.id', $ownedClients[3]->id)
            ->assertJsonPath('data.4.id', $ownedClients[4]->id)
            ->assertJsonPath('data.5.id', $sharedClients[0]->id)
            ->assertJsonPath('data.6.id', $sharedClients[1]->id)
            ->assertJsonPath('data.7.id', $sharedClients[2]->id)
            ->assertJsonPath('data.8.id', $sharedClients[3]->id)
            ->assertJsonPath('data.9.id', $publicClients[0]->id)
            ->assertJsonPath('data.10.id', $publicClients[1]->id)
            ->assertJsonPath('data.11.id', $publicClients[2]->id);
    }

    /** @test */
    public function index_paginates_activities()
    {
        $this->authenticatedWithSanctum();
        $this->user->givePermissionTo('manage-strava-clients');

        $ownedClients = StravaClient::factory()->count(5)->create(['user_id' => $this->user->id]);
        $sharedClients = StravaClient::factory()->count(4)
            ->afterCreating(fn (StravaClient $client) => $client->sharedUsers()->attach($this->user->id))
            ->create();
        $publicClients = StravaClient::factory()->count(3)
            ->afterCreating(fn (StravaClient $client) => $client->sharedUsers()->attach($this->user->id))
            ->create(['public' => true]);

        $response = $this->getJson(route('api.integration.strava.client.index', ['perPage' => 5, 'page' => 2]))
            ->assertStatus(200)
            ->assertJsonCount(5, 'data')
            ->assertJsonPath('data.0.id', $sharedClients[0]->id)
            ->assertJsonPath('data.1.id', $sharedClients[1]->id)
            ->assertJsonPath('data.2.id', $sharedClients[2]->id)
            ->assertJsonPath('data.3.id', $sharedClients[3]->id)
            ->assertJsonPath('data.4.id', $publicClients[0]->id);
    }

    /** @test */
    public function index_does_not_return_clients_you_cant_access()
    {
        $this->authenticatedWithSanctum();
        $this->user->givePermissionTo('manage-strava-clients');

        StravaClient::factory()->count(20)->create(['user_id' => User::factory()->create(), 'public' => false]); // Inaccessible due to another person owning them and not being shared
        $ownedClients = StravaClient::factory()->count(5)->create(['user_id' => $this->user->id]);
        $sharedClients = StravaClient::factory()->count(4)
            ->afterCreating(fn (StravaClient $client) => $client->sharedUsers()->attach($this->user->id))
            ->create();
        $publicClients = StravaClient::factory()->count(3)
            ->afterCreating(fn (StravaClient $client) => $client->sharedUsers()->attach($this->user->id))
            ->create(['public' => true]);

        $response = $this->getJson(route('api.integration.strava.client.index', ['perPage' => 1000, 'page' => 1]))
            ->assertStatus(200)
            ->assertJsonCount(12, 'data')
            ->assertJsonPath('data.0.id', $ownedClients[0]->id)
            ->assertJsonPath('data.1.id', $ownedClients[1]->id)
            ->assertJsonPath('data.2.id', $ownedClients[2]->id)
            ->assertJsonPath('data.3.id', $ownedClients[3]->id)
            ->assertJsonPath('data.4.id', $ownedClients[4]->id)
            ->assertJsonPath('data.5.id', $sharedClients[0]->id)
            ->assertJsonPath('data.6.id', $sharedClients[1]->id)
            ->assertJsonPath('data.7.id', $sharedClients[2]->id)
            ->assertJsonPath('data.8.id', $sharedClients[3]->id)
            ->assertJsonPath('data.9.id', $publicClients[0]->id)
            ->assertJsonPath('data.10.id', $publicClients[1]->id)
            ->assertJsonPath('data.11.id', $publicClients[2]->id);
    }

    /** @test */
    public function index_returns_just_the_system_client_if_user_does_not_have_permission()
    {
        $this->authenticatedWithSanctum();
        $this->user->revokePermissionTo('manage-strava-clients');
        $this->user->givePermissionTo('manage-global-settings');

        StravaClient::factory()->count(20)->create(['user_id' => User::factory()->create(), 'public' => false]); // Inaccessible due to another person owning them and not being shared
        $ownedClients = StravaClient::factory()->count(5)->create(['user_id' => $this->user->id]);
        $sharedClients = StravaClient::factory()->count(4)
            ->afterCreating(fn (StravaClient $client) => $client->sharedUsers()->attach($this->user->id))
            ->create();
        $publicClients = StravaClient::factory()->count(3)
            ->afterCreating(fn (StravaClient $client) => $client->sharedUsers()->attach($this->user->id))
            ->create(['public' => true]);
        $defaultClient = StravaClient::factory()->create(['user_id' => User::factory()->create()]);
        StravaToken::factory()->create(['user_id' => $this->user->id, 'strava_client_id' => $defaultClient->id, 'expires_at' => now()->addDay()]);
        \App\Settings\StravaClient::setValue($defaultClient->id);

        $response = $this->getJson(route('api.integration.strava.client.index', ['perPage' => 1000, 'page' => 1]))
            ->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $defaultClient->id);
    }

    /** @test */
    public function index_returns_a_429_if_no_system_client_set_and_user_does_not_have_permission()
    {
        $this->authenticatedWithSanctum();
        $this->user->revokePermissionTo('manage-strava-clients');
        $this->user->givePermissionTo('manage-global-settings');

        StravaClient::factory()->count(20)->create(['user_id' => User::factory()->create(), 'public' => false]); // Inaccessible due to another person owning them and not being shared
        $ownedClients = StravaClient::factory()->count(5)->create(['user_id' => $this->user->id]);
        $sharedClients = StravaClient::factory()->count(4)
            ->afterCreating(fn (StravaClient $client) => $client->sharedUsers()->attach($this->user->id))
            ->create();
        $publicClients = StravaClient::factory()->count(3)
            ->afterCreating(fn (StravaClient $client) => $client->sharedUsers()->attach($this->user->id))
            ->create(['public' => true]);
        $defaultClient = StravaClient::factory()->create();
        \App\Settings\StravaClient::setValue($defaultClient->id);

        $response = $this->getJson(route('api.integration.strava.client.index', ['perPage' => 1000, 'page' => 1]));
        $response->assertStatus(429);
        $this->assertInstanceOf(ClientNotAvailable::class, $response->exception);
        $this->assertEquals('No available clients found.', $response->exception->getMessage());
    }

    /** @test */
    public function you_must_be_authenticated()
    {
        $response = $this->getJson(route('api.integration.strava.client.index', ['perPage' => 1000, 'page' => 1]))
            ->assertUnauthorized();
    }
}
