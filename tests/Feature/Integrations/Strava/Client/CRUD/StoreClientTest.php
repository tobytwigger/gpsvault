<?php

namespace Feature\Integrations\Strava\Client\CRUD;

use App\Integrations\Strava\Client\Models\StravaClient;
use Illuminate\Support\Str;
use function route;
use Tests\TestCase;

class StoreClientTest extends TestCase
{
    /** @test */
    public function you_must_be_authenticated()
    {
        $response = $this->post(
            route('strava.client.store'),
            ['client_id' => 123, 'client_secret' => 'secret-123', 'name' => 'name123', 'description' => 'desc123']
        );
        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function it_returns_403_if_you_do_not_have_permission()
    {
        $this->authenticated();

        $response = $this->post(
            route('strava.client.store'),
            ['client_id' => 123, 'client_secret' => 'secret-123', 'name' => 'name123', 'description' => 'desc123']
        );

        $response->assertStatus(403);
        $response->assertSeeText('This action is unauthorized.');
    }

    /** @test */
    public function it_creates_the_client_and_redirects()
    {
        $this->authenticated();
        $this->user->givePermissionTo('manage-strava-clients');
        $this->assertDatabaseCount('strava_clients', 0);

        $response = $this->post(
            route('strava.client.store'),
            ['client_id' => 123, 'client_secret' => 'secret-123', 'name' => 'name123', 'description' => 'desc123']
        );

        $response->assertRedirect(route('integration.strava'));

        $this->assertDatabaseCount('strava_clients', 1);

        $client = StravaClient::first();
        $this->assertEquals('name123', $client->name);
        $this->assertEquals('desc123', $client->description);
        $this->assertEquals(123, $client->client_id);
        $this->assertEquals('secret-123', $client->client_secret);
        $this->assertEquals($this->user->id, $client->user_id);
        $this->assertEquals(true, $client->enabled);
        $this->assertEquals(false, $client->public);
        $this->assertEquals(100, $client->limit_15_min);
        $this->assertEquals(1000, $client->limit_daily);
    }

    /**
     * @test
     * @dataProvider validationDataProvider
     * @param mixed $attributes
     * @param mixed $key
     * @param mixed $error
     */
    public function it_validates($attributes, $key, $error)
    {
        $this->authenticated();
        $this->user->givePermissionTo('manage-strava-clients');

        $response = $this->post(route('strava.client.store'), $attributes);

        if (!$error) {
            $response->assertSessionHasNoErrors();
        } else {
            $response->assertSessionHasErrors([$key => $error]);
        }
    }

    public function validationDataProvider()
    {
        return [
            [['client_id' => 123, 'client_secret' => null], 'client_secret', 'The client secret field is required.'],
            [['client_id' => null, 'client_secret' => 'sec'], 'client_id', 'The client id field is required.'],
            [['client_id' => 123], 'client_secret', 'The client secret field is required.'],
            [['client_secret' => 'sec'], 'client_id', 'The client id field is required.'],
            [['client_id' => 'clientid', 'client_secret' => 'sec'], 'client_id', 'The client id must be an integer.'],
            [['client_id' => 123, 'client_secret' => ['test']], 'client_secret', 'The client secret must be a string.'],
            [['client_id' => 123, 'client_secret' => 'sec', 'name' => Str::random(260)], 'name', 'The name must not be greater than 255 characters.'],
            [['client_id' => 123, 'client_secret' => 'sec', 'description' => Str::random(70000)], 'description', 'The description must not be greater than 65535 characters.'],
            [['client_id' => 123, 'client_secret' => 'sec', 'name' => []], 'name', 'The name must be a string.'],
            [['client_id' => 123, 'client_secret' => 'sec', 'description' => []], 'description', 'The description must be a string.'],
        ];
    }
}
