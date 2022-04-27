<?php

namespace Feature\Integrations\Strava\Client\Invitation;

use App\Integrations\Strava\Client\Models\StravaClient;
use Tests\TestCase;

class LeaveClientTest extends TestCase
{

    /** @test */
    public function it_returns_a_404_if_the_client_is_not_found()
    {
        $this->authenticated();
        $this->user->givePermissionTo('manage-strava-clients');

        $response = $this->delete(route('strava.client.leave', 500));
        $response->assertStatus(404);
    }

    /** @test */
    public function you_must_be_authenticated()
    {
        $response = $this->delete(route('strava.client.leave', 500));
        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function it_returns_403_if_you_do_not_have_permission()
    {
        $this->authenticated();
        $client = StravaClient::factory()->create(['user_id' => $this->user->id]);

        $response = $this->delete(route('strava.client.leave', $client));
        $response->assertStatus(403);
        $response->assertSeeText('This action is unauthorized.');
    }

    /** @test */
    public function it_returns_a_403_if_you_are_not_part_of_the_client()
    {
        $this->authenticated();
        $this->user->givePermissionTo('manage-strava-clients');
        $client = StravaClient::factory()->create();

        $response = $this->delete(route('strava.client.leave', $client));
        $response->assertStatus(403);
        $response->assertSeeText('You do not have access to this client.');
    }

    /** @test */
    public function it_returns_a_403_if_you_own_the_client()
    {
        $this->authenticated();
        $this->user->givePermissionTo('manage-strava-clients');
        $client = StravaClient::factory()->create(['user_id' => $this->user->id]);

        $response = $this->delete(route('strava.client.leave', $client));
        $response->assertStatus(403);
        $response->assertSeeText('You own this client. If you no longer use this client, please delete it.');
    }

    /** @test */
    public function it_removes_you_from_the_client_and_redirects()
    {
        $this->authenticated();
        $this->user->givePermissionTo('manage-strava-clients');
        $client = StravaClient::factory()->create();
        $client->sharedUsers()->attach($this->user->id);

        $this->assertCount(1, $client->sharedUsers()->get());
        $response = $this->delete(route('strava.client.leave', $client));
        $response->assertRedirect(route('strava.client.index'));

        $this->assertCount(0, $client->sharedUsers()->get());
    }
}
