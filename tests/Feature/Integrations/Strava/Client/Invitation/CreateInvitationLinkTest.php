<?php

namespace Feature\Integrations\Strava\Client\Invitation;

use App\Integrations\Strava\Client\Models\StravaClient;
use Linkeys\UrlSigner\Models\Link;
use Tests\TestCase;

class CreateInvitationLinkTest extends TestCase
{
    /** @test */
    public function it_returns_a_404_if_the_client_is_not_found()
    {
        $this->authenticated();
        $this->user->givePermissionTo('manage-strava-clients');

        $response = $this->post(route('strava.client.invite', 500));
        $response->assertStatus(404);
    }

    /** @test */
    public function you_must_be_authenticated()
    {
        $response = $this->post(route('strava.client.invite', 500));
        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function it_returns_403_if_you_do_not_have_permission()
    {
        $this->authenticated();
        $client = StravaClient::factory()->create(['user_id' => $this->user->id]);

        $response = $this->post(route('strava.client.invite', $client));
        $response->assertStatus(403);
        $response->assertSeeText('This action is unauthorized.');
    }

    /** @test */
    public function it_returns_a_403_if_you_do_not_own_the_client()
    {
        $this->authenticated();
        $this->user->givePermissionTo('manage-strava-clients');
        $client = StravaClient::factory()->create();

        $response = $this->post(route('strava.client.invite', $client));
        $response->assertStatus(403);
        $response->assertSeeText('You can only invite users to a client you own.');
    }

    /** @test */
    public function it_updates_the_invitation_link_uuid_field_and_redirects()
    {
        $this->authenticated();
        $this->user->givePermissionTo('manage-strava-clients');
        $client = StravaClient::factory()->create(['user_id' => $this->user->id]);

        $this->assertNull($client->invitation_link_uuid);

        $response = $this->post(route('strava.client.invite', $client));
        $response->assertRedirect(route('integration.strava'));

        $client->refresh();
        $link = Link::where('uuid', $client->invitation_link_uuid)->firstOrFail();
        $this->assertFalse($link->expired());
        $this->assertEquals(['client_id' => $client->id], $link->data);
        $this->assertEquals(route('strava.client.accept', $client), $link->url);
        $this->assertNotNull($client->invitation_link_uuid);
    }
}
