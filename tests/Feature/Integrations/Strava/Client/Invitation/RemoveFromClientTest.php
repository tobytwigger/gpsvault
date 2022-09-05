<?php

namespace Feature\Integrations\Strava\Client\Invitation;

use App\Integrations\Strava\Client\Models\StravaClient;
use App\Models\User;
use Tests\TestCase;

class RemoveFromClientTest extends TestCase
{
    /** @test */
    public function it_returns_a_404_if_the_client_is_not_found()
    {
        $this->authenticated();
        $this->user->givePermissionTo('manage-strava-clients');

        $response = $this->delete(route('strava.client.remove', 500));
        $response->assertStatus(404);
    }

    /** @test */
    public function you_must_be_authenticated()
    {
        $response = $this->delete(route('strava.client.remove', 500));
        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function it_returns_403_if_you_do_not_have_permission()
    {
        $this->authenticated();
        $client = StravaClient::factory()->create(['user_id' => $this->user->id]);

        $response = $this->delete(route('strava.client.remove', $client));
        $response->assertStatus(403);
        $response->assertSeeText('This action is unauthorized.');
    }

    /** @test */
    public function it_returns_a_403_if_you_do_not_own_the_client()
    {
        $this->authenticated();
        $this->user->givePermissionTo('manage-strava-clients');
        $client = StravaClient::factory()->create();

        $response = $this->delete(route('strava.client.remove', $client));
        $response->assertStatus(403);
        $response->assertSeeText('You do not own this client.');
    }

    /** @test */
    public function it_removes_a_user_from_the_client_and_redirects()
    {
        $this->authenticated();
        $this->user->givePermissionTo('manage-strava-clients');
        $users = User::factory()->count(4)->create();
        $client = StravaClient::factory()->create(['user_id' => $this->user->id]);
        $client->sharedUsers()->attach($users->pluck('id'));

        $this->assertCount(4, $client->sharedUsers()->get());

        $response = $this->delete(route('strava.client.remove', $client), [
            'user_id' => $users[1]->id,
        ]);

        $response->assertRedirect(route('strava.client.index'));

        $this->assertCount(3, $client->sharedUsers()->get());
    }

    /** @test */
    public function it_validates_if_the_user_is_not_part_of_the_client()
    {
        $this->authenticated();
        $this->user->givePermissionTo('manage-strava-clients');
        $user = User::factory()->create();
        $client = StravaClient::factory()->create(['user_id' => $this->user->id]);

        $response = $this->delete(route('strava.client.remove', $client), [
            'user_id' => $user->id,
        ]);
        $response->assertSessionHasErrors([
            'user_id' => 'The selected user id is invalid.',
        ]);
    }

    /** @test */
    public function it_validates_if_the_user_id_is_not_given()
    {
        $this->authenticated();
        $this->user->givePermissionTo('manage-strava-clients');
        $client = StravaClient::factory()->create(['user_id' => $this->user->id]);

        $response = $this->delete(route('strava.client.remove', $client));
        $response->assertSessionHasErrors([
            'user_id' => 'The user id field is required.',
        ]);
    }

    /** @test */
    public function it_validates_if_the_user_id_is_not_an_integer()
    {
        $this->authenticated();
        $this->user->givePermissionTo('manage-strava-clients');
        $client = StravaClient::factory()->create(['user_id' => $this->user->id]);

        $response = $this->delete(route('strava.client.remove', $client), ['user_id' => 'this-isnt-an-int']);
        $response->assertSessionHasErrors([
            'user_id' => 'The user id must be an integer.',
        ]);
    }
}
