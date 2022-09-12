<?php

namespace Feature\Integrations\Strava\Client\Status;

use App\Integrations\Strava\Client\Models\StravaClient;
use function route;
use Tests\TestCase;

class EnableClientTest extends TestCase
{
    /** @test */
    public function it_returns_a_404_if_the_client_is_not_found()
    {
        $this->authenticated();
        $this->user->givePermissionTo('manage-strava-clients');

        $response = $this->post(route('strava.client.enable', 500));
        $response->assertStatus(404);
    }

    /** @test */
    public function you_must_be_authenticated()
    {
        $response = $this->post(route('strava.client.enable', 500));
        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function it_returns_403_if_you_do_not_have_permission()
    {
        $this->authenticated();
        $client = StravaClient::factory()->create(['user_id' => $this->user->id]);

        $response = $this->post(route('strava.client.enable', $client));
        $response->assertStatus(403);
        $response->assertSeeText('This action is unauthorized.');
    }

    /** @test */
    public function it_returns_a_403_if_you_do_not_own_the_client()
    {
        $this->authenticated();
        $this->user->givePermissionTo('manage-strava-clients');
        $client = StravaClient::factory()->create();

        $response = $this->post(route('strava.client.enable', $client));
        $response->assertStatus(403);
        $response->assertSeeText('You can only turn on a client you own.');
    }

    /** @test */
    public function it_does_nothing_if_client_already_enabled()
    {
        $this->authenticated();
        $this->user->givePermissionTo('manage-strava-clients');
        $client = StravaClient::factory()->create(['user_id' => $this->user->id, 'enabled' => true]);

        $response = $this->post(route('strava.client.enable', $client));
        $response->assertRedirect(route('integration.strava'));
        $this->assertTrue($client->refresh()->enabled);
    }

    /** @test */
    public function it_enables_the_client_and_redirects()
    {
        $this->authenticated();
        $this->user->givePermissionTo('manage-strava-clients');
        $client = StravaClient::factory()->create(['user_id' => $this->user->id, 'enabled' => false]);

        $response = $this->post(route('strava.client.enable', $client));
        $response->assertRedirect(route('integration.strava'));
        $this->assertTrue($client->refresh()->enabled);
    }
}
