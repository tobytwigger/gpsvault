<?php

namespace Unit\Integrations\Strava;

use App\Integrations\Strava\Client\Authentication\StravaToken;
use App\Integrations\Strava\Client\Exceptions\ClientNotAvailable;
use App\Integrations\Strava\Client\Models\StravaClient;
use App\Models\User;
use Tests\TestCase;

class UsesStravaTest extends TestCase
{

    /** @test */
    public function strava_tokens_are_deleted_on_user_delete(){
        $user = User::factory()->create();
        $tokens = StravaToken::factory()->count(5)->create(['user_id' => $user->id]);
        $this->assertDatabaseCount('strava_tokens', 5);
        $user->delete();
        $this->assertDatabaseCount('strava_tokens', 0);
    }

    /** @test */
    public function it_has_a_relationship_with_strava_tokens(){
        $user = User::factory()->create();
        $tokens = StravaToken::factory()->count(5)->create(['user_id' => $user->id]);
        StravaToken::factory()->count(5)->create();

        $this->assertCount(5, $user->stravaTokens);
        $this->assertTrue($tokens[0]->is($user->stravaTokens[0]));
        $this->assertTrue($tokens[1]->is($user->stravaTokens[1]));
        $this->assertTrue($tokens[2]->is($user->stravaTokens[2]));
        $this->assertTrue($tokens[3]->is($user->stravaTokens[3]));
        $this->assertTrue($tokens[4]->is($user->stravaTokens[4]));
    }

    /** @test */
    public function it_has_a_relationship_to_owned_clients(){
        $user = User::factory()->create();
        $clients = StravaClient::factory()->count(2)->create(['user_id' => $user->id]);
        StravaClient::factory()->count(5)->create();

        $this->assertCount(2, $user->ownedClients);
        $this->assertTrue($clients[0]->is($user->ownedClients[0]));
        $this->assertTrue($clients[1]->is($user->ownedClients[1]));
    }

    /** @test */
    public function it_has_a_relationship_to_shared_clients(){
        $user = User::factory()->create();
        $clients = StravaClient::factory()->count(2)->create();
        $user->sharedClients()->attach($clients);
        StravaClient::factory()->count(5)->create();

        $this->assertCount(2, $user->sharedClients);
        $this->assertTrue($clients[0]->is($user->sharedClients[0]));
        $this->assertTrue($clients[1]->is($user->sharedClients[1]));
    }

    /** @test */
    public function stravaClients_returns_all_strava_clients_the_user_can_see(){
        $user = User::factory()->create();

        $client1 = StravaClient::factory()->create(['user_id' => $user->id]);
        $client2 = StravaClient::factory()->create(['user_id' => $user->id]);
        $client3 = StravaClient::factory()->create();
        $client3->sharedUsers()->attach($user);
        $client4 = StravaClient::factory()->create(['public' => true]);

        StravaClient::factory()->count(5)->create(['public' => false]);

        $clients = $user->stravaClients();
        $this->assertCount(4, $clients);
        $this->assertTrue($client1->is($clients[0]));
        $this->assertTrue($client2->is($clients[1]));
        $this->assertTrue($client3->is($clients[2]));
        $this->assertTrue($client4->is($clients[3]));
    }

    /** @test */
    public function availableClient_returns_a_free_owned_client_if_you_have_permission(){
        $user = User::factory()->create();
        $user->givePermissionTo('manage-strava-clients');

        $client1 = StravaClient::factory()->create(['user_id' => $user->id]);
        $client2 = StravaClient::factory()->create(['user_id' => $user->id]);
        $client3 = StravaClient::factory()->create();
        $client3->sharedUsers()->attach($user);
        $client4 = StravaClient::factory()->create(['public' => true]);
        StravaToken::factory()->create(['user_id' => $user->id, 'strava_client_id' => $client1->id]);
        StravaToken::factory()->create(['user_id' => $user->id, 'strava_client_id' => $client2->id]);
        StravaToken::factory()->create(['user_id' => $user->id, 'strava_client_id' => $client3->id]);
        StravaToken::factory()->create(['user_id' => $user->id, 'strava_client_id' => $client4->id]);

        StravaClient::factory()->count(5)->create(['public' => false]);

        $client = $user->availableClient();
        $this->assertInstanceOf(StravaClient::class, $client);
        $this->assertTrue($client1->is($client));
    }

    /** @test */
    public function availableClient_takes_into_account_exclusions_for_owned_clients(){
        $user = User::factory()->create();
        $user->givePermissionTo('manage-strava-clients');

        $client1 = StravaClient::factory()->create(['user_id' => $user->id]);
        $client2 = StravaClient::factory()->create(['user_id' => $user->id]);
        $client3 = StravaClient::factory()->create();
        $client3->sharedUsers()->attach($user);
        $client4 = StravaClient::factory()->create(['public' => true]);
        StravaToken::factory()->create(['user_id' => $user->id, 'strava_client_id' => $client1->id]);
        StravaToken::factory()->create(['user_id' => $user->id, 'strava_client_id' => $client2->id]);
        StravaToken::factory()->create(['user_id' => $user->id, 'strava_client_id' => $client3->id]);
        StravaToken::factory()->create(['user_id' => $user->id, 'strava_client_id' => $client4->id]);

        StravaClient::factory()->count(5)->create(['public' => false]);

        $client = $user->availableClient([$client1->id]);
        $this->assertInstanceOf(StravaClient::class, $client);
        $this->assertTrue($client2->is($client));
    }

    /** @test */
    public function availableClient_returns_a_free_shared_client_if_permission_and_do_not_have_free_owned_client(){
        $user = User::factory()->create();
        $user->givePermissionTo('manage-strava-clients');

        $client3 = StravaClient::factory()->create();
        $client3->sharedUsers()->attach($user);
        $client4 = StravaClient::factory()->create();
        $client4->sharedUsers()->attach($user);
        $client5 = StravaClient::factory()->create(['public' => true]);
        StravaToken::factory()->create(['user_id' => $user->id, 'strava_client_id' => $client3->id]);
        StravaToken::factory()->create(['user_id' => $user->id, 'strava_client_id' => $client4->id]);
        StravaToken::factory()->create(['user_id' => $user->id, 'strava_client_id' => $client5->id]);

        StravaClient::factory()->count(5)->create(['public' => false]);

        $client = $user->availableClient();
        $this->assertInstanceOf(StravaClient::class, $client);
        $this->assertTrue($client3->is($client));
    }

    /** @test */
    public function availableClient_takes_into_account_exclusions_for_shared_clients(){
        $user = User::factory()->create();
        $user->givePermissionTo('manage-strava-clients');

        $client3 = StravaClient::factory()->create();
        $client3->sharedUsers()->attach($user);
        $client4 = StravaClient::factory()->create();
        $client4->sharedUsers()->attach($user);
        $client5 = StravaClient::factory()->create(['public' => true]);
        StravaToken::factory()->create(['user_id' => $user->id, 'strava_client_id' => $client3->id]);
        StravaToken::factory()->create(['user_id' => $user->id, 'strava_client_id' => $client4->id]);
        StravaToken::factory()->create(['user_id' => $user->id, 'strava_client_id' => $client5->id]);

        StravaClient::factory()->count(5)->create(['public' => false]);

        $client = $user->availableClient([$client3->id]);
        $this->assertInstanceOf(StravaClient::class, $client);
        $this->assertTrue($client4->is($client));
    }

    /** @test */
    public function availableClient_returns_a_public_client_if_permission_and_no_manage_strava_permission(){
        $this->markTestIncomplete();

        $user = User::factory()->create();
        $user->givePermissionTo('use-public-strava-clients');

        $client1 = StravaClient::factory()->create(['user_id' => $user->id]);
        $client2 = StravaClient::factory()->create(['user_id' => $user->id]);
        $client3 = StravaClient::factory()->create();
        $client3->sharedUsers()->attach($user);
        $client4 = StravaClient::factory()->create(['public' => true]);
        $client5 = StravaClient::factory()->create(['public' => true]);
        StravaToken::factory()->create(['user_id' => $user->id, 'strava_client_id' => $client1->id]);
        StravaToken::factory()->create(['user_id' => $user->id, 'strava_client_id' => $client2->id]);
        StravaToken::factory()->create(['user_id' => $user->id, 'strava_client_id' => $client3->id]);
        StravaToken::factory()->create(['user_id' => $user->id, 'strava_client_id' => $client4->id]);
        StravaToken::factory()->create(['user_id' => $user->id, 'strava_client_id' => $client5->id]);

        StravaClient::factory()->count(5)->create(['public' => false]);

        $client = $user->availableClient();
        $this->assertInstanceOf(StravaClient::class, $client);
        $this->assertTrue($client4->is($client));
    }

    /** @test */
    public function availableClient_takes_into_account_exclusions_for_public_clients(){
        $user = User::factory()->create();
        $user->givePermissionTo('use-public-strava-clients');

        $client1 = StravaClient::factory()->create(['user_id' => $user->id]);
        $client2 = StravaClient::factory()->create(['user_id' => $user->id]);
        $client3 = StravaClient::factory()->create();
        $client3->sharedUsers()->attach($user);
        $client4 = StravaClient::factory()->create(['public' => true]);
        $client5 = StravaClient::factory()->create(['public' => true]);
        StravaToken::factory()->create(['user_id' => $user->id, 'strava_client_id' => $client1->id]);
        StravaToken::factory()->create(['user_id' => $user->id, 'strava_client_id' => $client2->id]);
        StravaToken::factory()->create(['user_id' => $user->id, 'strava_client_id' => $client3->id]);
        StravaToken::factory()->create(['user_id' => $user->id, 'strava_client_id' => $client4->id]);
        StravaToken::factory()->create(['user_id' => $user->id, 'strava_client_id' => $client5->id]);

        StravaClient::factory()->count(5)->create(['public' => false]);

        $client = $user->availableClient([$client4->id]);
        $this->assertInstanceOf(StravaClient::class, $client);
        $this->assertTrue($client5->is($client));
    }

    /** @test */
    public function availableClient_returns_a_public_client_if_permission_and_no_owned_or_shared_available_clients(){
        $user = User::factory()->create();
        $user->givePermissionTo('manage-strava-clients');
        $user->givePermissionTo('use-public-strava-clients');

        $client1 = StravaClient::factory()->create(['user_id' => $user->id, 'limit_daily' => 5, 'used_daily_calls' => 100]);
        $client2 = StravaClient::factory()->create();
        $client3 = StravaClient::factory()->create(['limit_daily' => 100, 'used_daily_calls' => 1800]);
        $client3->sharedUsers()->attach($user);
        $client4 = StravaClient::factory()->create(['public' => true, 'limit_daily' => 5, 'used_daily_calls' => 100]);
        $client5 = StravaClient::factory()->create(['public' => true]);
        StravaToken::factory()->create(['user_id' => $user->id, 'strava_client_id' => $client1->id]);
        StravaToken::factory()->create(['user_id' => $user->id, 'strava_client_id' => $client2->id]);
        StravaToken::factory()->create(['user_id' => $user->id, 'strava_client_id' => $client3->id]);
        StravaToken::factory()->create(['user_id' => $user->id, 'strava_client_id' => $client4->id]);
        StravaToken::factory()->create(['user_id' => $user->id, 'strava_client_id' => $client5->id]);

        StravaClient::factory()->count(5)->create(['public' => false]);

        $client = $user->availableClient();
        $this->assertInstanceOf(StravaClient::class, $client);
        $this->assertTrue($client5->is($client));
    }

    /** @test */
    public function availableClient_returns_the_system_client_if_no_public_permission(){
        $user = User::factory()->create();

        $client1 = StravaClient::factory()->create(['user_id' => $user->id]);
        $client3 = StravaClient::factory()->create();
        $client3->sharedUsers()->attach($user);
        $client5 = StravaClient::factory()->create(['public' => true]);
        StravaToken::factory()->create(['user_id' => $user->id, 'strava_client_id' => $client1->id]);
        StravaToken::factory()->create(['user_id' => $user->id, 'strava_client_id' => $client3->id]);
        StravaToken::factory()->create(['user_id' => $user->id, 'strava_client_id' => $client5->id]);
        $systemClient = StravaClient::factory()->create();
        \App\Settings\StravaClient::setValue($systemClient->id);
        StravaToken::factory()->create(['user_id' => $user->id, 'strava_client_id' => $systemClient->id]);

        $client = $user->availableClient();
        $this->assertInstanceOf(StravaClient::class, $client);
        $this->assertTrue($systemClient->is($client));
    }

    /** @test */
    public function availableClient_returns_the_system_client_if_no_public_clients_available(){
        $user = User::factory()->create();
        $user->givePermissionTo('use-public-strava-clients');

        $client5 = StravaClient::factory()->full()->create(['public' => true]);
        $systemClient = StravaClient::factory()->create();
        \App\Settings\StravaClient::setValue($systemClient->id);
        StravaToken::factory()->create(['user_id' => $user->id, 'strava_client_id' => $client5->id]);
        StravaToken::factory()->create(['user_id' => $user->id, 'strava_client_id' => $systemClient->id]);

        $client = $user->availableClient();
        $this->assertInstanceOf(StravaClient::class, $client);
        $this->assertTrue($systemClient->is($client));
    }

    /** @test */
    public function availableClient_throws_an_exception_if_no_clients_found(){
        $this->expectException(ClientNotAvailable::class);
        $this->expectExceptionMessage('No system client has been set.');

        $user = User::factory()->create();

        \App\Settings\StravaClient::setValue(null);

        $user->availableClient();
    }

    /** @test */
    public function availableClient_throws_an_exception_if_system_client_is_full(){
        $this->expectException(ClientNotAvailable::class);
        $this->expectExceptionMessage('No available clients found.');

        $user = User::factory()->create();

        $systemClient = StravaClient::factory()->create(['limit_daily' => 5, 'used_daily_calls' => 100]);
        StravaToken::factory()->create(['user_id' => $user->id, 'strava_client_id' => $systemClient->id]);

        \App\Settings\StravaClient::setValue($systemClient->id);

        $user->availableClient();
    }

    /** @test */
    public function it_ignores_clients_that_are_not_connected(){
        $this->expectException(ClientNotAvailable::class);
        $this->expectExceptionMessage('No available clients found.');

        $user = User::factory()->create();
        $systemClient = StravaClient::factory()->create();
        \App\Settings\StravaClient::setValue($systemClient->id);

        $client = $user->availableClient();
    }

}
