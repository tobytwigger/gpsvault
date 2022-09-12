<?php

namespace Unit\Integrations\Strava\Client\Client\Resources;

use App\Integrations\Strava\Client\Authentication\Authenticator;
use App\Integrations\Strava\Client\Client\Resources\Webhook;
use App\Integrations\Strava\Client\Client\StravaRequestHandler;
use App\Models\User;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Tests\TestCase;
use Tests\Utils\ConnectsToStrava;

class WebhookTest extends TestCase
{
    use ConnectsToStrava;

    /** @test */
    public function webhook_exists_returns_true_if_any_webhooks_returned()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('manage-strava-clients');

        $client = $this->connectToStrava($user);

        $response = new Response(200, [], json_encode([
            ['webhook_name' => 'test'],
            ['webhook_name_2' => 'test'],
        ]));
        $guzzleClient = $this->prophesize(Client::class);
        $guzzleClient->request(
            'GET',
            'push_subscriptions',
            ['json' => [
                'client_id' => $client->client_id,
                'client_secret' => $client->client_secret,
            ]]
        )
            ->shouldBeCalled()
            ->willReturn($response);
        $authenticator = new Authenticator($user, $guzzleClient->reveal());

        $resource = new Webhook($user, new StravaRequestHandler(
            $authenticator,
            $user,
            $guzzleClient->reveal()
        ));

        $this->assertTrue($resource->webhookExists($client));
    }

    /** @test */
    public function webhook_exists_returns_false_if_no_webhooks_returned()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('manage-strava-clients');

        $client = $this->connectToStrava($user);

        $response = new Response(200, [], json_encode([]));
        $guzzleClient = $this->prophesize(Client::class);
        $guzzleClient->request(
            'GET',
            'push_subscriptions',
            ['json' => [
                'client_id' => $client->client_id,
                'client_secret' => $client->client_secret,
            ]]
        )
            ->shouldBeCalled()
            ->willReturn($response);
        $authenticator = new Authenticator($user, $guzzleClient->reveal());

        $resource = new Webhook($user, new StravaRequestHandler(
            $authenticator,
            $user,
            $guzzleClient->reveal()
        ));

        $this->assertFalse($resource->webhookExists($client));
    }

    /** @test */
    public function todo_createWebhook_creates_a_webhook(){
        $this->markTestSkipped();
    }
}
