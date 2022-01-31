<?php

namespace Feature\Integrations\Strava\Client\Login;

use App\Integrations\Strava\Client\Authentication\StravaToken;
use App\Integrations\Strava\Client\Models\StravaClient;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class LogOutOfStravaTest extends TestCase
{

    /** @test */
    public function you_must_be_authenticated(){
        $response = $this->post(route('strava.client.logout', 500));
        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function it_returns_a_404_if_the_client_is_not_found(){
        $this->authenticated();
        $this->user->givePermissionTo('manage-strava-clients');
        $client = StravaClient::factory()->create(['user_id' => $this->user->id]);
        $client->delete();

        $response = $this->post(route('strava.client.logout', $client->id));
        $response->assertStatus(404);
    }

    /** @test */
    public function it_returns_403_if_you_do_not_have_permission(){
        $this->authenticated();
        $client = StravaClient::factory()->create(['user_id' => $this->user->id]);

        $response = $this->post(route('strava.client.logout', $client));
        $response->assertStatus(403);
        $response->assertSeeText('This action is unauthorized.');
    }

    /** @test */
    public function it_returns_403_if_you_do_not_have_access_to_the_client(){
        $this->authenticated();
        $this->user->givePermissionTo('manage-strava-clients');
        $client = StravaClient::factory()->create();
        StravaToken::factory()->count(2)->create(['user_id' => $this->user->id]);
        StravaToken::factory()->count(2)->create(['strava_client_id' => $client->id]);

        $response = $this->post(route('strava.client.logout', $client));
        $response->assertStatus(403);
        $response->assertSeeText('You do not have access to this client.');
    }

    /** @test */
    public function it_returns_403_if_you_are_not_logged_into_the_client(){
        $this->authenticated();
        $this->user->givePermissionTo('manage-strava-clients');
        $client = StravaClient::factory()->create(['user_id' => $this->user->id]);
        StravaToken::factory()->count(2)->create(['user_id' => $this->user->id]);
        StravaToken::factory()->count(2)->create(['strava_client_id' => $client->id]);

        $response = $this->post(route('strava.client.logout', $client));
        $response->assertStatus(403);
        $response->assertSeeText('You are not logged into this client.');
    }

    /** @test */
    public function it_deletes_all_tokens_for_that_client_and_redirects_to_index(){
        $this->authenticated();
        $this->user->givePermissionTo('manage-strava-clients');
        $client = StravaClient::factory()->create(['user_id' => $this->user->id]);
        StravaToken::factory()->count(2)->create([
            'user_id' => $this->user->id,
            'strava_client_id' => $client->id
        ]);
        StravaToken::factory()->count(2)->create(['user_id' => $this->user->id]);
        StravaToken::factory()->count(2)->create(['strava_client_id' => $client->id]);

        $response = $this->post(route('strava.client.logout', $client));
        $response->assertRedirect(route('strava.client.index'));
        $this->assertDatabaseCount('strava_tokens', 4);
    }

    /** @test */
    public function it_works_for_shared_clients(){
        $this->authenticated();
        $this->user->givePermissionTo('manage-strava-clients');
        $client = StravaClient::factory()->create();
        $client->sharedUsers()->attach($this->user->id);
        StravaToken::factory()->create([
            'user_id' => $this->user->id,
            'strava_client_id' => $client->id
        ]);

        $response = $this->post(route('strava.client.logout', $client));
        $response->assertRedirect(route('strava.client.index'));

        $this->assertDatabaseCount('strava_tokens', 0);
    }

    /** @test */
    public function it_works_for_public_clients(){
        $this->authenticated();
        $this->user->givePermissionTo('manage-strava-clients');
        $client = StravaClient::factory()->create(['public' => true]);
        StravaToken::factory()->create([
            'user_id' => $this->user->id,
            'strava_client_id' => $client->id
        ]);

        $response = $this->post(route('strava.client.logout', $client));
        $response->assertRedirect(route('strava.client.index'));

        $this->assertDatabaseCount('strava_tokens', 0);
    }

}
