<?php

namespace Feature\Integrations\Strava;

use App\Integrations\Strava\Client\Authentication\StravaToken;
use App\Integrations\Strava\Client\Models\StravaClient;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class StravaOverviewTest extends TestCase
{
    /** @test */
    public function todo_scaffolding_rest_of_the_inertia_props()
    {
        $this->markTestIncomplete();
    }

    /** @test */
    public function todo_scaffolding_more_edge_case_testing_for_clients_and_the_values_returned()
    {
        $this->markTestIncomplete();
    }

    /** @test */
    public function todo_tests_for_the_paginator_copied_from_client_index()
    {
        $this->markTestIncomplete();
    }

    /** @test */
    public function it_loads_the_inertia_component()
    {
        $this->authenticated();
        $this->user->givePermissionTo('manage-strava-clients');

        $response = $this->get(route('integration.strava'));
        $response->assertStatus(200);
        $response->assertInertia(
            fn (Assert $page) => $page
                ->component('Integrations/Strava/Index')
        );
    }

    /** @test */
    public function you_must_be_authenticated()
    {
        $response = $this->get(route('integration.strava'));
        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function it_returns_if_a_client_is_available()
    {
        $this->authenticated();
        $this->user->givePermissionTo('manage-strava-clients');

        $this->get(route('integration.strava'))
            ->assertStatus(200)
            ->assertInertia(
                fn (Assert $page) => $page
                    ->component('Integrations/Strava/Index')
                    ->where('isAvailable', false)
            );

        StravaClient::factory()->create(['user_id' => $this->user->id, 'enabled' => true]);

        $this->get(route('integration.strava'))
            ->assertStatus(200)
            ->assertInertia(
                fn (Assert $page) => $page
                    ->component('Integrations/Strava/Index')
                    ->where('isAvailable', true)
            );
    }

    /** @test */
    public function it_returns_if_a_client_is_connected()
    {
        $this->authenticated();
        $this->user->givePermissionTo('manage-strava-clients');

        $stravaClient = StravaClient::factory()->create(['user_id' => $this->user->id, 'enabled' => true]);

        $this->get(route('integration.strava'))
            ->assertStatus(200)
            ->assertInertia(
                fn (Assert $page) => $page
                    ->component('Integrations/Strava/Index')
                    ->where('isConnected', false)
            );

        StravaToken::factory()->create(['user_id' => $this->user->id, 'strava_client_id' => $stravaClient->id]);

        $this->get(route('integration.strava'))
            ->assertStatus(200)
            ->assertInertia(
                fn (Assert $page) => $page
                    ->component('Integrations/Strava/Index')
                    ->where('isConnected', true)
            );
    }

    /** @test */
    public function it_returns_if_a_connected_client_has_rate_limited_space()
    {
        $this->authenticated();
        $this->user->givePermissionTo('manage-strava-clients');

        $stravaClient = StravaClient::factory()->create(['user_id' => $this->user->id, 'enabled' => true]);
        StravaToken::factory()->create(['user_id' => $this->user->id, 'strava_client_id' => $stravaClient->id]);

        $this->get(route('integration.strava'))
            ->assertStatus(200)
            ->assertInertia(
                fn (Assert $page) => $page
                    ->component('Integrations/Strava/Index')
                    ->where('clientHasSpace', true)
            );

        $stravaClient->used_15_min_calls = 10000;
        $stravaClient->used_daily_calls = 10000;
        $stravaClient->save();

        $this->get(route('integration.strava'))
            ->assertStatus(200)
            ->assertInertia(
                fn (Assert $page) => $page
                    ->component('Integrations/Strava/Index')
                    ->where('clientHasSpace', false)
            );
    }

    /** @test */
    public function it_returns_the_default_client()
    {
        $this->authenticated();
        $this->user->givePermissionTo('manage-strava-clients');
        $this->user->givePermissionTo('manage-global-settings');

        $stravaClient = StravaClient::factory()->create(['user_id' => $this->user->id, 'enabled' => true]);
        \App\Settings\StravaClient::setValue($stravaClient->id);

        $this->get(route('integration.strava'))
            ->assertStatus(200)
            ->assertInertia(
                fn (Assert $page) => $page
                    ->component('Integrations/Strava/Index')
                    ->has(
                        'defaultClient',
                        fn (Assert $page) => $page
                            ->where('id', $stravaClient->id)
                            ->etc()
                    )
            );
    }

    /** @test */
    public function it_returns_if_a_client_is_available_if_no_permission()
    {
        $this->authenticated();
        $this->user->revokePermissionTo('manage-strava-clients');
        $this->user->givePermissionTo('manage-global-settings');

        $this->get(route('integration.strava'))
            ->assertStatus(200)
            ->assertInertia(
                fn (Assert $page) => $page
                    ->component('Integrations/Strava/Index')
                    ->where('isAvailable', false)
            );

        $defaultClient = StravaClient::factory()->create(['user_id' => $this->user->id, 'enabled' => true, 'public' => true]);
        \App\Settings\StravaClient::setValue($defaultClient->id);

        $this->get(route('integration.strava'))
            ->assertStatus(200)
            ->assertInertia(
                fn (Assert $page) => $page
                    ->component('Integrations/Strava/Index')
                    ->where('isAvailable', true)
            );
    }

    /** @test */
    public function it_returns_if_a_client_is_connected_if_no_permission()
    {
        $this->authenticated();
        $this->user->revokePermissionTo('manage-strava-clients');
        $this->user->givePermissionTo('manage-global-settings');

        $stravaClient = StravaClient::factory()->create(['user_id' => $this->user->id, 'enabled' => true]);
        \App\Settings\StravaClient::setValue($stravaClient->id);

        $this->get(route('integration.strava'))
            ->assertStatus(200)
            ->assertInertia(
                fn (Assert $page) => $page
                    ->component('Integrations/Strava/Index')
                    ->where('isConnected', false)
            );

        StravaToken::factory()->create(['user_id' => $this->user->id, 'strava_client_id' => $stravaClient->id]);

        $this->get(route('integration.strava'))
            ->assertStatus(200)
            ->assertInertia(
                fn (Assert $page) => $page
                    ->component('Integrations/Strava/Index')
                    ->where('isConnected', true)
            );
    }

    /** @test */
    public function it_returns_if_a_connected_client_has_rate_limited_space_if_no_permission()
    {
        $this->authenticated();
        $this->user->revokePermissionTo('manage-strava-clients');
        $this->user->givePermissionTo('manage-global-settings');

        $stravaClient = StravaClient::factory()->create(['user_id' => $this->user->id, 'enabled' => true]);
        StravaToken::factory()->create(['user_id' => $this->user->id, 'strava_client_id' => $stravaClient->id]);
        \App\Settings\StravaClient::setValue($stravaClient->id);

        $this->get(route('integration.strava'))
            ->assertStatus(200)
            ->assertInertia(
                fn (Assert $page) => $page
                    ->component('Integrations/Strava/Index')
                    ->where('clientHasSpace', true)
            );

        $stravaClient->used_15_min_calls = 10000;
        $stravaClient->used_daily_calls = 10000;
        $stravaClient->save();

        $this->get(route('integration.strava'))
            ->assertStatus(200)
            ->assertInertia(
                fn (Assert $page) => $page
                    ->component('Integrations/Strava/Index')
                    ->where('clientHasSpace', false)
            );
    }

    /** @test */
    public function it_returns_the_default_client_if_no_permission()
    {
        $this->authenticated();
        $this->user->revokePermissionTo('manage-strava-clients');
        $this->user->givePermissionTo('manage-global-settings');

        $stravaClient = StravaClient::factory()->create(['user_id' => $this->user->id, 'enabled' => true, 'public' => true]);
        \App\Settings\StravaClient::setValue($stravaClient->id);

        $this->get(route('integration.strava'))
            ->assertStatus(200)
            ->assertInertia(
                fn (Assert $page) => $page
                    ->component('Integrations/Strava/Index')
                    ->has(
                        'defaultClient',
                        fn (Assert $page) => $page
                            ->where('id', $stravaClient->id)
                            ->etc()
                    )
            );
    }

    /** @test */
    public function it_returns_if_a_client_is_available_if_no_permission_and_no_client_set()
    {
        $this->authenticated();
        $this->user->revokePermissionTo('manage-strava-clients');

        $this->get(route('integration.strava'))
            ->assertStatus(200)
            ->assertInertia(
                fn (Assert $page) => $page
                    ->component('Integrations/Strava/Index')
                    ->where('isAvailable', false)
            );
    }

    /** @test */
    public function it_returns_if_a_client_is_connected_if_no_permission_and_no_client_set()
    {
        $this->authenticated();
        $this->user->revokePermissionTo('manage-strava-clients');

        $this->get(route('integration.strava'))
            ->assertStatus(200)
            ->assertInertia(
                fn (Assert $page) => $page
                    ->component('Integrations/Strava/Index')
                    ->where('isConnected', false)
            );
    }

    /** @test */
    public function it_returns_if_a_connected_client_has_rate_limited_space_if_no_permission_and_no_client_set()
    {
        $this->authenticated();
        $this->user->revokePermissionTo('manage-strava-clients');

        $this->get(route('integration.strava'))
            ->assertStatus(200)
            ->assertInertia(
                fn (Assert $page) => $page
                    ->component('Integrations/Strava/Index')
                    ->where('clientHasSpace', false)
            );
    }

    /** @test */
    public function it_returns_the_default_client_if_no_permission_and_no_client_set()
    {
        $this->authenticated();
        $this->user->revokePermissionTo('manage-strava-clients');
        $this->user->givePermissionTo('manage-global-settings');

        $this->get(route('integration.strava'))
            ->assertStatus(200)
            ->assertInertia(
                fn (Assert $page) => $page
                    ->component('Integrations/Strava/Index')
                    ->where('defaultClient', null)
            );
    }
}
