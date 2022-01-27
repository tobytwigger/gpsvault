<?php

namespace Feature\Integrations\Strava\Client\CRUD;

use App\Integrations\Strava\Client\Models\StravaClient;
use Illuminate\Support\Str;
use Tests\TestCase;
use function route;

class UpdateClientTest extends TestCase
{

    /** @test */
    public function it_returns_a_404_if_the_client_is_not_found(){
        $this->authenticated();
        $this->user->givePermissionTo('manage-strava-clients');

        $response = $this->patch(route('strava.client.update', 500));
        $response->assertStatus(404);
    }

    /** @test */
    public function you_must_be_authenticated(){
        $client = StravaClient::factory()->create();

        $response = $this->patch(
            route('strava.client.update', $client),
            ['client_secret' => 'secret-123', 'name' => 'name123', 'description' => 'desc123']
        );
        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function it_returns_403_if_you_do_not_have_permission(){
        $this->authenticated();
        $client = StravaClient::factory()->create(['user_id' => $this->user->id]);

        $response = $this->patch(
            route('strava.client.update', $client),
            ['client_secret' => 'secret-123', 'name' => 'name123', 'description' => 'desc123']
        );

        $response->assertStatus(403);
        $response->assertSeeText('This action is unauthorized.');
    }


    /** @test */
    public function it_returns_a_403_if_you_do_not_own_the_client(){
        $this->authenticated();
        $this->user->givePermissionTo('manage-strava-clients');
        $client = StravaClient::factory()->create();

        $response = $this->patch(
            route('strava.client.update', $client),
            ['client_secret' => 'secret-123', 'name' => 'name123', 'description' => 'desc123']
        );

        $response->assertStatus(403);
        $response->assertSeeText('You can only update a client you own.');
    }

    /** @test */
    public function it_updates_the_client_and_redirects(){
        $this->authenticated();
        $this->user->givePermissionTo('manage-strava-clients');
        $client = StravaClient::factory()->create(['user_id' => $this->user->id, 'client_id' => 123, 'client_secret' => 'secret-123', 'name' => 'name123', 'description' => 'desc123']);

        $this->assertEquals('name123', $client->name);
        $this->assertEquals('desc123', $client->description);
        $this->assertEquals('secret-123', $client->client_secret);

        $response = $this->patch(
            route('strava.client.update', $client),
            ['client_secret' => 'secret-123-updated', 'name' => 'name123-updated', 'description' => 'desc123-updated']
        );

        $response->assertRedirect(route('strava.client.index'));

        $client->refresh();
        $this->assertEquals('name123-updated', $client->name);
        $this->assertEquals('desc123-updated', $client->description);
        $this->assertEquals(123, $client->client_id);
        $this->assertEquals('secret-123-updated', $client->client_secret);
        $this->assertEquals($this->user->id, $client->user_id);
        $this->assertEquals(true, $client->enabled);
        $this->assertEquals(false, $client->public);
        $this->assertEquals(100, $client->limit_15_min);
        $this->assertEquals(1000, $client->limit_daily);
    }

    /** @test */
    public function the_client_id_cannot_be_updated(){
        $this->authenticated();
        $this->user->givePermissionTo('manage-strava-clients');
        $client = StravaClient::factory()->create(['user_id' => $this->user->id, 'client_id' => 123]);

        $response = $this->patch(
            route('strava.client.update', $client),
            ['client_id' => 123456]
        );

        $response->assertRedirect(route('strava.client.index'));

        $client->refresh();
        $this->assertEquals(123, $client->client_id);
    }

    /**
     * @test
     * @dataProvider validationDataProvider
     */
    public function it_validates($attributes, $key, $error){
        $this->authenticated();
        $this->user->givePermissionTo('manage-strava-clients');

        $client = StravaClient::factory()->create(['user_id' => $this->user->id]);

        $response = $this->patch(route('strava.client.update', $client), $attributes);

        if(!$error) {
            $response->assertSessionHasNoErrors();
        } else {
            $response->assertSessionHasErrors([$key => $error]);
        }
    }

    public function validationDataProvider()
    {
        return [
            [['client_secret' => null], 'client_secret', 'The client secret must be a string.'],
            [['client_secret' => ['test']], 'client_secret', 'The client secret must be a string.'],
            [['name' => Str::random(260)], 'name', 'The name must not be greater than 255 characters.'],
            [['description' => Str::random(70000)], 'description', 'The description must not be greater than 65535 characters.'],
            [['name' => []], 'name', 'The name must be a string.'],
            [['description' => []], 'description', 'The description must be a string.'],
        ];
    }

}
