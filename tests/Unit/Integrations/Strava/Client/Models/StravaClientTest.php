<?php

namespace Unit\Integrations\Strava\Client\Models;

use App\Integrations\Strava\Client\Models\StravaClient;
use App\Integrations\Strava\Client\Authentication\StravaToken;
use App\Models\User;
use Carbon\Carbon;
use Linkeys\UrlSigner\Models\Link;
use Tests\TestCase;

class StravaClientTest extends TestCase
{

    /** @test */
    public function it_adds_a_user_id_on_creation(){
        $this->authenticated();
        $client = StravaClient::factory()->make(['user_id' => null]);
        $client->save();

        $this->assertEquals($this->user->id, $client->refresh()->user_id);
    }

    /** @test */
    public function it_adds_a_random_token_for_webhook_verify_on_creation(){
        $client = StravaClient::factory()->make(['webhook_verify_token' => null]);
        $client->save();

        $this->assertIsString($client->refresh()->webhook_verify_token);
        $this->assertEquals(20, strlen($client->refresh()->webhook_verify_token));
    }

    /** @test */
    public function excluding_excludes_all_given_ids_from_results(){
        $client1 = StravaClient::factory()->create();
        $client2 = StravaClient::factory()->create();
        $client3 = StravaClient::factory()->create();
        $client4 = StravaClient::factory()->create();

        $clients = StravaClient::excluding([$client1->id, $client4->id])->get();
        $this->assertCount(2, $clients);
        $this->assertTrue($client2->is($clients[0]));
        $this->assertTrue($client3->is($clients[1]));
    }

    /** @test */
    public function excluding_does_nothing_if_excluding_array_is_empty(){
        $client1 = StravaClient::factory()->create();
        $client2 = StravaClient::factory()->create();
        $client3 = StravaClient::factory()->create();
        $client4 = StravaClient::factory()->create();

        $clients = StravaClient::excluding([])->get();
        $this->assertCount(4, $clients);
        $this->assertTrue($client1->is($clients[0]));
        $this->assertTrue($client2->is($clients[1]));
        $this->assertTrue($client3->is($clients[2]));
        $this->assertTrue($client4->is($clients[3]));
    }

    /** @test */
    public function enabled_limits_queries_to_only_enabled_clients(){
        $client1 = StravaClient::factory()->create(['enabled' => true]);
        $client2 = StravaClient::factory()->create(['enabled' => true]);
        $client3 = StravaClient::factory()->create(['enabled' => false]);
        $client4 = StravaClient::factory()->create(['enabled' => true]);

        $clients = StravaClient::enabled()->get();
        $this->assertCount(3, $clients);
        $this->assertTrue($client1->is($clients[0]));
        $this->assertTrue($client2->is($clients[1]));
        $this->assertTrue($client4->is($clients[2]));
    }

    public function it_has_a_relationship_with_tokens()
    {
        $client = StravaClient::factory()->create();
        $token1 = StravaToken::factory()->create(['strava_client_id' => $client->id]);
        $token2 = StravaToken::factory()->create(['strava_client_id' => $client->id]);
        StravaToken::factory()->create();

        $tokens = $client->tokens()->get();
        $this->assertCount(2, $tokens);
        $this->assertTrue($token1->is($tokens[0]));
        $this->assertTrue($token2->is($tokens[1]));
    }

    /** @test */
    public function it_has_an_owner(){
        $user = User::factory()->create();
        $client = StravaClient::factory()->create(['user_id' => $user->id]);

        $this->assertTrue($user->is($client->owner));
    }

    /** @test */
    public function it_has_a_relationship_with_shared_users(){
        $user = User::factory()->create();
        $user2 = User::factory()->create();
        $user3 = User::factory()->create();
        $user4 = User::factory()->create();
        $client = StravaClient::factory()->create(['user_id' => $user->id]);
        $client->sharedUsers()->attach($user2);
        $client->sharedUsers()->attach($user3);

        $this->assertCount(2, $client->sharedUsers);
        $this->assertTrue($user2->is($client->sharedUsers[0]));
        $this->assertTrue($user3->is($client->sharedUsers[1]));
    }

    /** @test */
    public function forUser_returns_any_clients_the_user_owns_has_access_to_or_is_public(){
        $user = User::factory()->create();

        $client1 = StravaClient::factory()->create(['user_id' => $user->id]);
        $client2 = StravaClient::factory()->create(['user_id' => $user->id]);
        $client3 = StravaClient::factory()->create();
        $client3->sharedUsers()->attach($user);
        $client4 = StravaClient::factory()->create(['public' => true]);

        StravaClient::factory()->count(5)->create(['public' => false]);

        $clients = StravaClient::forUser($user->id)->get();
        $this->assertCount(4, $clients);
        $this->assertTrue($client1->is($clients[0]));
        $this->assertTrue($client2->is($clients[1]));
        $this->assertTrue($client3->is($clients[2]));
        $this->assertTrue($client4->is($clients[3]));
    }

    /** @test */
    public function has_spaces_returns_true_if_both_are_below_the_limit(){
        $client = StravaClient::factory()->create([
            'used_15_min_calls' => 50,
            'used_daily_calls' => 250,
            'limit_daily' => 1000,
            'limit_15_min' => 100
        ]);
        $this->assertTrue($client->hasSpaces());
    }

    /** @test */
    public function has_spaces_returns_false_if_15_mins_is_over(){
        $client = StravaClient::factory()->create([
            'used_15_min_calls' => 90,
            'used_daily_calls' => 250,
            'limit_daily' => 1000,
            'limit_15_min' => 50
        ]);
        $this->assertFalse($client->hasSpaces());
    }

    /** @test */
    public function has_spaces_returns_false_if_daily_is_over(){
        $client = StravaClient::factory()->create([
            'used_15_min_calls' => 200,
            'used_daily_calls' => 855,
            'limit_daily' => 800,
            'limit_15_min' => 500
        ]);
        $this->assertFalse($client->hasSpaces());
    }

    /** @test */
    public function has_spaces_returns_false_if_both_are_over(){
        $client = StravaClient::factory()->create([
            'used_15_min_calls' => 200,
            'used_daily_calls' => 855,
            'limit_daily' => 800,
            'limit_15_min' => 50
        ]);
        $this->assertFalse($client->hasSpaces());
    }

    /** @test */
    public function public_scopes_only_public_clients(){
        $client1 = StravaClient::factory()->create(['public' => true]);
        $client2 = StravaClient::factory()->create(['public' => true]);
        $client3 = StravaClient::factory()->create(['public' => false]);
        $client4 = StravaClient::factory()->create(['public' => true]);

        $clients = StravaClient::public()->get();
        $this->assertCount(3, $clients);
        $this->assertTrue($client1->is($clients[0]));
        $this->assertTrue($client2->is($clients[1]));
        $this->assertTrue($client4->is($clients[2]));
    }

    /** @test */
    public function withSpaces_scopes_to_all_clients_with_spaces(){
        // Daily no spaces
        StravaClient::factory()->create(['used_15_min_calls' => 200, 'limit_15_min' => 500, 'used_daily_calls' => 855, 'limit_daily' => 100]);
        // 15 min no spaces
        StravaClient::factory()->create(['used_15_min_calls' => 100, 'limit_15_min' => 50, 'used_daily_calls' => 855, 'limit_daily' => 1000]);
        // Both no spaces
        StravaClient::factory()->create(['used_15_min_calls' => 500, 'limit_15_min' => 200, 'used_daily_calls' => 1200, 'limit_daily' => 1000]);
        // Daily matching
        StravaClient::factory()->create(['used_15_min_calls' => 200, 'limit_15_min' => 500, 'used_daily_calls' => 100, 'limit_daily' => 100]);
        // 15 mins matching
        StravaClient::factory()->create(['used_15_min_calls' => 100, 'limit_15_min' => 100, 'used_daily_calls' => 855, 'limit_daily' => 1000]);
        // both matching
        StravaClient::factory()->create(['used_15_min_calls' => 200, 'limit_15_min' => 200, 'used_daily_calls' => 1000, 'limit_daily' => 1000]);

        $client1 = StravaClient::factory()->create(['used_15_min_calls' => 199, 'used_daily_calls' => 999, 'limit_daily' => 1000, 'limit_15_min' => 200]);
        $client2 = StravaClient::factory()->create(['used_15_min_calls' => 100, 'used_daily_calls' => 855, 'limit_daily' => 1000, 'limit_15_min' => 200]);
        $client3 = StravaClient::factory()->create(['used_15_min_calls' => 50, 'used_daily_calls' => 20, 'limit_daily' => 1000, 'limit_15_min' => 200]);
        $client4 = StravaClient::factory()->create(['used_15_min_calls' => 20, 'used_daily_calls' => 0, 'limit_daily' => 1000, 'limit_15_min' => 200]);
        $client5 = StravaClient::factory()->create(['used_15_min_calls' => 0, 'used_daily_calls' => 100, 'limit_daily' => 1000, 'limit_15_min' => 200]);

        $clients = StravaClient::withSpaces()->get();

        $this->assertCount(5, $clients);
        $this->assertTrue($client1->is($clients[0]));
        $this->assertTrue($client2->is($clients[1]));
        $this->assertTrue($client3->is($clients[2]));
        $this->assertTrue($client4->is($clients[3]));
        $this->assertTrue($client5->is($clients[4]));
    }

    /** @test */
    public function connected_returns_true_if_tokens_are_connected_and_valid_for_the_given_user(){
        $user = User::factory()->create();

        $client = StravaClient::factory()->create();
        $this->assertFalse($client->isConnected($user->id));

        $token = StravaToken::factory()->create(['strava_client_id' => $client->id, 'user_id' => $user->id, 'expires_at' => Carbon::now()->addDay()]);
        $this->assertTrue($client->isConnected($user->id));
    }

    /** @test */
    public function connected_returns_false_if_no_available_tokens(){
        $user = User::factory()->create();

        $client = StravaClient::factory()->create();

        StravaToken::factory()->create(['strava_client_id' => $client->id]);
        StravaToken::factory()->create(['strava_client_id' => $client->id, 'user_id' => $user->id, 'disabled' => true]);
        StravaToken::factory()->create(['strava_client_id' => $client->id, 'user_id' => $user->id, 'expires_at' => Carbon::now()->subHours(5)]);

        $this->assertFalse($client->isConnected($user->id));
    }

    /** @test */
    public function is_connected_returns_true_if_tokens_are_connected_and_valid_for_the_logged_in_user(){
        $user = User::factory()->create();
        $this->be($user);

        $client = StravaClient::factory()->create();
        $this->assertFalse($client->is_connected);

        $token = StravaToken::factory()->create(['strava_client_id' => $client->id, 'user_id' => $user->id, 'expires_at' => Carbon::now()->addDay()]);
        $this->assertTrue($client->is_connected);
    }

    /** @test */
    public function is_connected_returns_false_if_no_available_tokens(){
        $user = User::factory()->create();
        $this->be($user);

        $client = StravaClient::factory()->create();

        StravaToken::factory()->create(['strava_client_id' => $client->id]);
        StravaToken::factory()->create(['strava_client_id' => $client->id, 'user_id' => $user->id, 'disabled' => true]);
        StravaToken::factory()->create(['strava_client_id' => $client->id, 'user_id' => $user->id, 'expires_at' => Carbon::now()->subHours(5)]);

        $this->assertFalse($client->is_connected);
    }

    /** @test */
    public function is_connected_returns_false_if_user_logged_out(){
        $client = StravaClient::factory()->create();
        $token = StravaToken::factory()->create(['strava_client_id' => $client->id]);
        $this->assertFalse($client->is_connected);
    }

    /** @test */
    public function getInvitationLink_returns_a_link_when_uuid_set(){
        $client = StravaClient::factory()->create();

        $link = \Linkeys\UrlSigner\Facade\UrlSigner::generate(
            'http://test.com',
            ['client_id' => $client->id],
            '+24 hours'
        );
        $client->invitation_link_uuid = $link->uuid;
        $client->save();

        $this->assertTrue($link->is($client->getInvitationLink()));
    }

    /** @test */
    public function getInvitationLink_returns_null_if_no_uuid_set(){
        $client = StravaClient::factory()->create();

        $this->assertNull($client->getInvitationLink());
    }

    /** @test */
    public function invitation_link_expired_attribute_is_added_to_the_client(){
        $client = StravaClient::factory()->create();

        $link = \Linkeys\UrlSigner\Facade\UrlSigner::generate(
            'http://test.com',
            ['client_id' => $client->id],
            '-1 minute'
        );
        $client->invitation_link_uuid = $link->uuid;
        $client->save();

        $this->assertTrue($client->invitation_link_expired);
    }

    /** @test */
    public function invitation_link_attribute_is_added_to_the_client(){
        $client = StravaClient::factory()->create();

        $link = \Linkeys\UrlSigner\Facade\UrlSigner::generate(
            'http://test.com',
            ['client_id' => $client->id],
            '-1 minute'
        );
        $client->invitation_link_uuid = $link->uuid;
        $client->save();

        $this->assertEquals($link->getFullUrl(), $client->invitation_link);
    }

    /** @test */
    public function invitation_link_expires_at_attribute_is_added_to_the_client(){
        $client = StravaClient::factory()->create();

        $link = \Linkeys\UrlSigner\Facade\UrlSigner::generate(
            'http://test.com',
            ['client_id' => $client->id],
            '+1 hour'
        );
        $client->invitation_link_uuid = $link->uuid;
        $client->save();

        $this->assertEquals($link->expiry->toIso8601String(), $client->invitation_link_expires_at->toIso8601String());
    }

    /** @test */
    public function connected_only_returns_connected_clients(){
        $user = User::factory()->create();

        $client1 = StravaClient::factory()->create();
        $client2 = StravaClient::factory()->create();
        StravaToken::factory()->create(['user_id' => $user->id, 'strava_client_id' => $client2->id]);
        $client3 = StravaClient::factory()->create();
        StravaToken::factory()->create(['user_id' => $user->id, 'strava_client_id' => $client3->id]);

        $this->assertCount(2, StravaClient::connected($user->id)->get());

        $this->assertTrue($client2->is(StravaClient::connected($user->id)->orderBy('id')->get()[0]));
        $this->assertTrue($client3->is(StravaClient::connected($user->id)->orderBy('id')->get()[1]));
    }


}
