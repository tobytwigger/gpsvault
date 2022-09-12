<?php

namespace Feature\Integrations\Strava\Client\CRUD;

use App\Integrations\Strava\Client\Models\StravaClient;
use function route;
use Tests\TestCase;

class DestroyClientTest extends TestCase
{
    /** @test */
    public function it_returns_a_404_if_the_client_is_not_found()
    {
        $this->authenticated();
        $this->user->givePermissionTo('manage-strava-clients');

        $response = $this->delete(route('strava.client.destroy', 500));
        $response->assertStatus(404);
    }

    /** @test */
    public function you_must_be_authenticated()
    {
        $response = $this->delete(route('strava.client.destroy', 500));
        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function it_returns_403_if_you_do_not_have_permission()
    {
        $this->authenticated();
        $client = StravaClient::factory()->create(['user_id' => $this->user->id]);

        $response = $this->delete(route('strava.client.destroy', $client));
        $response->assertStatus(403);
        $response->assertSeeText('This action is unauthorized.');
    }

    /** @test */
    public function it_returns_a_403_if_you_do_not_own_the_client()
    {
        $this->authenticated();
        $this->user->givePermissionTo('manage-strava-clients');
        $client = StravaClient::factory()->create();

        $response = $this->delete(route('strava.client.destroy', $client));
        $response->assertStatus(403);
        $response->assertSeeText('You can only delete a client you own.');
    }

    /** @test */
    public function it_deletes_the_client_and_redirects()
    {
        $this->authenticated();
        $this->user->givePermissionTo('manage-strava-clients');
        $client = StravaClient::factory()->create(['user_id' => $this->user->id]);

        $response = $this->delete(route('strava.client.destroy', $client));
        $response->assertRedirect(route('integration.strava'));

        $this->assertNull(StravaClient::find($client->id));
    }
}
