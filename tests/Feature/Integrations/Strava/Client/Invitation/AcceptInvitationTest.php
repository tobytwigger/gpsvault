<?php

namespace Feature\Integrations\Strava\Client\Invitation;

use App\Integrations\Strava\Client\Models\StravaClient;
use Ramsey\Uuid\Uuid;
use Tests\TestCase;

class AcceptInvitationTest extends TestCase
{

    /** @test */
    public function it_returns_a_404_if_the_link_is_invalid()
    {
        $this->authenticated();
        $this->user->givePermissionTo('manage-strava-clients');
        $client = StravaClient::factory()->create(['user_id' => $this->user->id]);

        $response = $this->get(route('strava.client.accept', $client));
        $response->assertStatus(404);
    }

    /** @test */
    public function it_returns_a_404_if_the_client_is_not_found()
    {
        $this->authenticated();
        $this->user->givePermissionTo('manage-strava-clients');
        $client = StravaClient::factory()->create(['user_id' => $this->user->id]);
        $client->delete();

        $response = $this->get(route('strava.client.accept', $client->id));
        $response->assertStatus(404);
    }

    /** @test */
    public function you_must_be_authenticated()
    {
        $response = $this->get(route('strava.client.accept', 500));
        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function it_returns_403_if_you_do_not_have_permission()
    {
        $this->authenticated();
        $client = StravaClient::factory()->create(['user_id' => $this->user->id]);

        $link = \Linkeys\UrlSigner\Facade\UrlSigner::generate(
            route('strava.client.accept', ['client' => $client->id]),
            ['client_id' => $client->id],
            '+24 hours'
        );
        $client->invitation_link_uuid = $link->uuid;
        $client->save();

        $response = $this->get($link->getFullUrl());
        $response->assertStatus(403);
        $response->assertSeeText('This action is unauthorized.');
    }

    /** @test */
    public function it_returns_a_403_if_the_link_uuid_does_not_match_the_one_on_the_client()
    {
        $this->authenticated();
        $this->user->givePermissionTo('manage-strava-clients');
        $client = StravaClient::factory()->create();
        $link = \Linkeys\UrlSigner\Facade\UrlSigner::generate(
            route('strava.client.accept', ['client' => $client->id]),
            ['client_id' => $client->id],
            '+24 hours'
        );
        $client->invitation_link_uuid = Uuid::uuid4()->toString();
        $client->save();

        $response = $this->get($link->getFullUrl());
        $response->assertStatus(403);
        $response->assertSeeText('The invitation is not valid or has expired.');
    }

    /** @test */
    public function it_returns_a_403_if_you_are_the_owner()
    {
        $this->authenticated();
        $this->user->givePermissionTo('manage-strava-clients');
        $client = StravaClient::factory()->create(['user_id' => $this->user->id]);
        $link = \Linkeys\UrlSigner\Facade\UrlSigner::generate(
            route('strava.client.accept', ['client' => $client->id]),
            ['client_id' => $client->id],
            '+24 hours'
        );
        $client->invitation_link_uuid = $link->uuid;
        $client->save();

        $response = $this->get($link->getFullUrl());
        $response->assertStatus(403);
        $response->assertSeeText('You cannot accept your own invitation.');
    }

    /** @test */
    public function it_returns_a_403_if_you_already_have_access()
    {
        $this->authenticated();
        $this->user->givePermissionTo('manage-strava-clients');
        $client = StravaClient::factory()->create();
        $client->sharedUsers()->attach($this->user);

        $link = \Linkeys\UrlSigner\Facade\UrlSigner::generate(
            route('strava.client.accept', ['client' => $client->id]),
            ['client_id' => $client->id],
            '+24 hours'
        );
        $client->invitation_link_uuid = $link->uuid;
        $client->save();

        $response = $this->get($link->getFullUrl());
        $response->assertStatus(403);
        $response->assertSeeText('You have already accepted this invitation.');
    }

    /** @test */
    public function it_attaches_you_to_the_client_and_redirects()
    {
        $this->authenticated();
        $this->user->givePermissionTo('manage-strava-clients');
        $client = StravaClient::factory()->create();

        $this->assertCount(0, $client->sharedUsers()->get());

        $link = \Linkeys\UrlSigner\Facade\UrlSigner::generate(
            route('strava.client.accept', ['client' => $client->id]),
            ['client_id' => $client->id],
            '+24 hours'
        );
        $client->invitation_link_uuid = $link->uuid;
        $client->save();

        $response = $this->get($link->getFullUrl());
        $response->assertRedirect(route('strava.client.index'));

        $this->assertCount(1, $client->sharedUsers()->get());
        $this->assertEquals($this->user->id, $client->sharedUsers()->get()->first()->id);
    }
}
