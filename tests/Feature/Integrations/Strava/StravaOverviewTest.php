<?php

namespace Feature\Integrations\Strava;

use App\Integrations\Strava\Client\Models\StravaClient;
use App\Models\User;
use Illuminate\Support\Collection;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class StravaOverviewTest extends TestCase
{
    /** @test */
    public function it_loads_the_inertia_component()
    {
        $this->authenticated();

        $response = $this->get(route('integration.strava'));
        $response->assertStatus(200);
        $response->assertInertia(
            fn(Assert $page) => $page
                ->component('Integrations/Strava/Index')
        );
    }

    /** @test */
    public function you_must_be_authenticated()
    {
        $response = $this->get(route('integration.strava'));
        $response->assertRedirect(route('login'));
    }

    public function assertPaginatesClients(Collection $models, string $responseAttribute)
    {
        $this->get(route('integration.strava', [
            $responseAttribute . '_per_page' => 5,
            $responseAttribute . '_page' => 2,
        ]))->assertInertia(
            fn(Assert $page) => $page
                ->component('Integrations/Strava/Index')
                ->has(
                    $responseAttribute . 'Clients',
                    fn(Assert $page) => $page
                        ->has('data', 5)
                        ->has('data.0', fn(Assert $page) => $page->where('name', $models[5]->name)->etc())
                        ->has('data.1', fn(Assert $page) => $page->where('id', $models[6]->id)->etc())
                        ->has('data.2', fn(Assert $page) => $page->where('id', $models[7]->id)->etc())
                        ->has('data.3', fn(Assert $page) => $page->where('id', $models[8]->id)->etc())
                        ->has('data.4', fn(Assert $page) => $page->where('id', $models[9]->id)->etc())
                        ->etc()
                )
        );

        $this->get(route('integration.strava', [
            $responseAttribute . '_per_page' => 3,
            $responseAttribute . '_page' => 4,
        ]))->assertInertia(
            fn(Assert $page) => $page
                ->component('Integrations/Strava/Index')
                ->has(
                    $responseAttribute . 'Clients',
                    fn(Assert $page) => $page
                        ->has('data', 3)
                        ->has('data.0', fn(Assert $page) => $page->where('name', $models[9]->name)->etc())
                        ->has('data.1', fn(Assert $page) => $page->where('id', $models[10]->id)->etc())
                        ->has('data.2', fn(Assert $page) => $page->where('id', $models[11]->id)->etc())
                        ->etc()
                )
        );
    }

    /** @test */
    public function it_paginates_through_owned_clients()
    {
        $this->authenticated();
        $this->user->givePermissionTo('manage-strava-clients');

        $ownedClients = StravaClient::factory()->count(20)->create(['user_id' => $this->user->id]);
        $sharedClients = StravaClient::factory()->count(20)
            ->afterCreating(fn(StravaClient $client) => $client->sharedUsers()->attach($this->user->id))
            ->create();
        $publicClients = StravaClient::factory()->count(20)
            ->afterCreating(fn(StravaClient $client) => $client->sharedUsers()->attach($this->user->id))
            ->create(['public' => true]);

        $this->assertPaginatesClients($ownedClients, 'owned');
    }

    /** @test */
    public function it_paginates_through_shared_clients()
    {
        $this->authenticated();
        $this->user->givePermissionTo('manage-strava-clients');

        $ownedClients = StravaClient::factory()->count(20)->create(['user_id' => $this->user->id]);
        $sharedClients = StravaClient::factory()->count(20)
            ->afterCreating(fn(StravaClient $client) => $client->sharedUsers()->attach($this->user->id))
            ->create();
        $publicClients = StravaClient::factory()->count(20)
            ->afterCreating(fn(StravaClient $client) => $client->sharedUsers()->attach($this->user->id))
            ->create(['public' => true]);

        $this->assertPaginatesClients($sharedClients, 'shared');
    }

    /** @test */
    public function it_paginates_through_public_clients()
    {
        $this->authenticated();
        $this->user->givePermissionTo('manage-strava-clients');

        $ownedClients = StravaClient::factory()->count(20)->create(['user_id' => $this->user->id]);
        $sharedClients = StravaClient::factory()->count(20)->create();
        $publicClients = StravaClient::factory()->count(20)->create(['public' => true]);

        $this->assertPaginatesClients($publicClients, 'public');
    }

    /** @test */
    public function it_can_paginate_through_all_at_the_same_time()
    {
        $this->authenticated();
        $this->user->givePermissionTo('manage-strava-clients');

        $ownedClients = StravaClient::factory()->count(20)->create(['user_id' => $this->user->id]);
        $sharedClients = StravaClient::factory()->count(20)
            ->afterCreating(fn(StravaClient $client) => $client->sharedUsers()->attach($this->user->id))
            ->create();
        $publicClients = StravaClient::factory()->count(20)
            ->afterCreating(fn(StravaClient $client) => $client->sharedUsers()->attach($this->user->id))
            ->create(['public' => true]);

        $this->get(route('integration.strava', [
            'owned_per_page' => 5, 'owned_page' => 2,
            'shared_per_page' => 4, 'shared_page' => 3,
            'public_per_page' => 3, 'public_page' => 5,
        ]))->assertInertia(
            fn(Assert $page) => $page
                ->component('Integrations/Strava/Index')
                ->has(
                    'ownedClients',
                    fn(Assert $page) => $page
                        ->has('data', 5)
                        ->has('data.0', fn(Assert $page) => $page->where('name', $ownedClients[5]->name)->etc())
                        ->has('data.1', fn(Assert $page) => $page->where('id', $ownedClients[6]->id)->etc())
                        ->has('data.2', fn(Assert $page) => $page->where('id', $ownedClients[7]->id)->etc())
                        ->has('data.3', fn(Assert $page) => $page->where('id', $ownedClients[8]->id)->etc())
                        ->has('data.4', fn(Assert $page) => $page->where('id', $ownedClients[9]->id)->etc())
                        ->etc()
                )
                ->has(
                    'sharedClients',
                    fn(Assert $page) => $page
                        ->has('data', 4)
                        ->has('data.0', fn(Assert $page) => $page->where('name', $sharedClients[8]->name)->etc())
                        ->has('data.1', fn(Assert $page) => $page->where('id', $sharedClients[9]->id)->etc())
                        ->has('data.2', fn(Assert $page) => $page->where('id', $sharedClients[10]->id)->etc())
                        ->has('data.3', fn(Assert $page) => $page->where('id', $sharedClients[11]->id)->etc())
                        ->etc()
                )
                ->has(
                    'publicClients',
                    fn(Assert $page) => $page
                        ->has('data', 3)
                        ->has('data.0', fn(Assert $page) => $page->where('name', $publicClients[12]->name)->etc())
                        ->has('data.1', fn(Assert $page) => $page->where('id', $publicClients[13]->id)->etc())
                        ->has('data.2', fn(Assert $page) => $page->where('id', $publicClients[14]->id)->etc())
                        ->etc()
                )
        );
    }

    /** @test */
    public function it_returns_the_expected_data_for_each_client()
    {
        $this->authenticated();
        $this->user->givePermissionTo('manage-strava-clients');

        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $ownedClient = StravaClient::factory()->create(['user_id' => $this->user->id]);
        $ownedClient->sharedUsers()->attach([$user1->id, $user2->id]);
        $sharedClient = StravaClient::factory()->afterCreating(fn(StravaClient $client) => $client->sharedUsers()->attach($this->user->id))
            ->create();
        $publicClient = StravaClient::factory()
            ->afterCreating(fn(StravaClient $client) => $client->sharedUsers()->attach($this->user->id))
            ->create(['public' => true]);

        $this->get(route('integration.strava', [
            'owned_per_page' => 1, 'owned_page' => 1,
            'shared_per_page' => 1, 'shared_page' => 1,
            'public_per_page' => 1, 'public_page' => 1,
        ]))
            ->assertInertia(
                fn(Assert $page) => $page
                    ->component('Integrations/Strava/Index')
                    ->has(
                        'ownedClients',
                        fn(Assert $page) => $page
                            ->has('data', 1)
                            ->has(
                                'data.0',
                                fn(Assert $page) => $page
                                    ->where('id', $ownedClient->id)
                                    ->where('name', $ownedClient->name)
                                    ->where('user_id', $ownedClient->user_id)
                                    ->where('description', $ownedClient->description)
                                    ->where('enabled', $ownedClient->enabled)
                                    ->where('public', $ownedClient->public)
                                    ->where('webhook_verify_token', $ownedClient->webhook_verify_token)
                                    ->where('invitation_link_uuid', $ownedClient->invitation_link_uuid)
                                    ->where('used_15_min_calls', $ownedClient->used_15_min_calls)
                                    ->where('used_daily_calls', $ownedClient->used_daily_calls)
                                    ->where('limit_15_min', $ownedClient->limit_15_min)
                                    ->where('limit_daily', $ownedClient->limit_daily)
                                    ->where('created_at', $ownedClient->created_at->toIso8601String())
                                    ->where('updated_at', $ownedClient->updated_at->toIso8601String())
                                    ->where('is_connected', $ownedClient->is_connected)
                                    ->where('invitation_link', $ownedClient->invitation_link)
                                    ->where('invitation_link_expired', $ownedClient->invitation_link_expired)
                                    ->where('invitation_link_expires_at', $ownedClient->invitation_link_expires_at)
                                    ->where('client_id', $ownedClient->client_id)
                                    ->where('client_secret', $ownedClient->client_secret)
                                    ->has('shared_users', 2)
                                    ->has(
                                        'shared_users.0',
                                        fn(Assert $page) => $page
                                            ->where('id', $user1->id)
                                            ->where('name', $user1->name)
                                            ->where('email', $user1->email)
                                    )
                                    ->has(
                                        'shared_users.1',
                                        fn(Assert $page) => $page
                                            ->where('id', $user2->id)
                                            ->where('name', $user2->name)
                                            ->where('email', $user2->email)
                                    )
                            )
                            ->etc()
                    )
                    ->has(
                        'sharedClients',
                        fn(Assert $page) => $page
                            ->has('data', 1)
                            ->has(
                                'data.0',
                                fn(Assert $page) => $page
                                    ->where('id', $sharedClient->id)
                                    ->where('name', $sharedClient->name)
                                    ->where('description', $sharedClient->description)
                                    ->where('user', $sharedClient->owner->name)
                                    ->where('enabled', $sharedClient->enabled)
                                    ->where('used_15_min_calls', $sharedClient->used_15_min_calls)
                                    ->where('used_daily_calls', $sharedClient->used_daily_calls)
                                    ->where('limit_15_min', $sharedClient->limit_15_min)
                                    ->where('limit_daily', $sharedClient->limit_daily)
                                    ->where('client_id', $sharedClient->client_id)
                                    ->where('is_connected', $sharedClient->is_connected)
                            )
                            ->etc()
                    )
                    ->has(
                        'publicClients',
                        fn(Assert $page) => $page
                            ->has('data', 1)
                            ->has(
                                'data.0',
                                fn(Assert $page) => $page
                                    ->where('id', $publicClient->id)
                                    ->where('name', $publicClient->name)
                                    ->where('description', $publicClient->description)
                                    ->where('used_15_min_calls', $publicClient->used_15_min_calls)
                                    ->where('used_daily_calls', $publicClient->used_daily_calls)
                                    ->where('limit_15_min', $publicClient->limit_15_min)
                                    ->where('limit_daily', $publicClient->limit_daily)
                                    ->where('client_id', $publicClient->client_id)
                                    ->where('is_connected', $publicClient->is_connected)
                            )
                            ->etc()
                    )
            );
    }

}
