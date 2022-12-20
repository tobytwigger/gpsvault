<?php

namespace Feature\Activity;

use App\Models\Activity;
use Carbon\Carbon;
use Illuminate\Testing\AssertableJsonString;
use Tests\TestCase;

class ActivitySearchTest extends TestCase
{
    /** @test */
    public function it_returns_all_activities_sorted_by_updated_at_when_no_query_given()
    {
        $this->authenticated();

        $activities = Activity::factory()->count(5)->create(['user_id' => $this->user->id]);
        $activities[0]->updated_at = Carbon::now()->subDays(2);
        $activities[1]->updated_at = Carbon::now()->subDays(3);
        $activities[2]->updated_at = Carbon::now()->subDays(6);
        $activities[3]->updated_at = Carbon::now()->subDays(4);
        $activities[4]->updated_at = Carbon::now()->subDays(1);
        $activities->map->save();

        $response = $this->getJson(route('activity.search', ['query' => null, 'perPage' => 100]));
        $response->assertOk();
        $response->assertJsonCount(5, 'data');
        $json = $response->decodeResponseJson()['data'];

        $this->assertEquals($activities[4]->id, $json[0]['id']);
        $this->assertEquals($activities[0]->id, $json[1]['id']);
        $this->assertEquals($activities[1]->id, $json[2]['id']);
        $this->assertEquals($activities[3]->id, $json[3]['id']);
        $this->assertEquals($activities[2]->id, $json[4]['id']);
    }

    /** @test */
    public function it_paginates_the_responses()
    {
        $this->authenticated();

        $activities = Activity::factory()->count(50)->create(['user_id' => $this->user->id]);
        $activities[0]->updated_at = Carbon::now()->addDays(10);
        $activities[1]->updated_at = Carbon::now()->addDays(9);
        $activities[2]->updated_at = Carbon::now()->addDays(8);
        $activities[3]->updated_at = Carbon::now()->addDays(7);
        $activities[4]->updated_at = Carbon::now()->addDays(6);
        $activities[5]->updated_at = Carbon::now()->addDays(5);
        $activities[6]->updated_at = Carbon::now()->addDays(4);
        $activities[7]->updated_at = Carbon::now()->addDays(3);
        $activities[8]->updated_at = Carbon::now()->addDays(2);
        $activities[9]->updated_at = Carbon::now()->addDays(1);
        $activities->map->save();

        $response = $this->getJson(route('activity.search', ['query' => null, 'perPage' => 5, 'page' => 1]));
        $response->assertOk();
        $response->assertJsonCount(5, 'data');
        $json = $response->decodeResponseJson()['data'];

        $this->assertEquals($activities[0]->id, $json[0]['id']);
        $this->assertEquals($activities[1]->id, $json[1]['id']);
        $this->assertEquals($activities[2]->id, $json[2]['id']);
        $this->assertEquals($activities[3]->id, $json[3]['id']);
        $this->assertEquals($activities[4]->id, $json[4]['id']);

        $response = $this->getJson(route('activity.search', ['query' => null, 'perPage' => 5, 'page' => 2]));
        $response->assertOk();
        $response->assertJsonCount(5, 'data');
        $json = $response->decodeResponseJson()['data'];

        $this->assertEquals($activities[5]->id, $json[0]['id']);
        $this->assertEquals($activities[6]->id, $json[1]['id']);
        $this->assertEquals($activities[7]->id, $json[2]['id']);
        $this->assertEquals($activities[8]->id, $json[3]['id']);
        $this->assertEquals($activities[9]->id, $json[4]['id']);
    }

    /** @test */
    public function it_limits_to_a_set_number_of_activities()
    {
        $this->authenticated();

        $activities = Activity::factory()->count(20)->create(['user_id' => $this->user->id]);
        foreach ($activities as $index => $activity) {
            $activity->updated_at = Carbon::now()->subDays($index);
            $activity->save();
        }

        $response = $this->getJson(route('activity.search', ['query' => null, 'perPage' => 11]));
        $response->assertJsonCount(11, 'data');
        $json = $response->decodeResponseJson()['data'];

        foreach ($activities->take(11) as $index => $activity) {
            $this->assertEquals($activity->id, $json[$index]['id']);
        }
    }

    /** @test */
    public function it_only_returns_your_activities()
    {
        $this->authenticated();

        $activities = Activity::factory()->count(5)->create(['user_id' => $this->user->id]);
        Activity::factory()->count(50)->create();
        $response = $this->getJson(route('activity.search', ['query' => null, 'perPage' => 100]));
        $response->assertJsonCount(5, 'data');
        $json = new AssertableJsonString($response->json('data'));

        $json->assertFragment(['id' => $activities[0]->id]);
        $json->assertFragment(['id' => $activities[1]->id]);
        $json->assertFragment(['id' => $activities[2]->id]);
        $json->assertFragment(['id' => $activities[3]->id]);
        $json->assertFragment(['id' => $activities[4]->id]);
    }

    /** @test */
    public function it_filters_by_name()
    {
        $this->authenticated();

        $activities = Activity::factory()->count(5)->create(['user_id' => $this->user->id, 'name' => 'My name']);
        $activity = Activity::factory()->create(['user_id' => $this->user->id, 'name' => 'This is a different name']);

        $response = $this->getJson(route('activity.search', ['query' => 'name', 'perPage' => 100]));
        $response->assertJsonCount(6, 'data');

        $response = $this->getJson(route('activity.search', ['query' => 'different', 'perPage' => 100]));
        $response->assertJsonCount(1, 'data');
    }

    /** @test */
    public function filtering_is_not_case_sensitive()
    {
        $this->authenticated();

        $activities = Activity::factory()->count(5)->create(['user_id' => $this->user->id, 'name' => 'My name']);
        $activity = Activity::factory()->create(['user_id' => $this->user->id, 'name' => 'This is a different Name']);

        $response = $this->getJson(route('activity.search', ['query' => 'name', 'perPage' => 100]));
        $response->assertJsonCount(6, 'data');
    }

    /** @test */
    public function you_must_be_authenticated()
    {
        $activities = Activity::factory()->count(5)->create();

        $this->getJson(route('activity.search', ['query' => null]))
            ->assertStatus(401);
    }

    /** @test */
    public function it_orders_the_results_by_updated_at()
    {
        $this->authenticated();

        $activity1 = Activity::factory()->create(['user_id' => $this->user->id, 'updated_at' => now()->subDay()]);
        $activity2 = Activity::factory()->create(['user_id' => $this->user->id, 'updated_at' => now()->subYear()]);
        $activity3 = Activity::factory()->create(['user_id' => $this->user->id, 'updated_at' => now()->subSecond()]);
        $activity4 = Activity::factory()->create(['user_id' => $this->user->id, 'updated_at' => now()->subMinutes(5)]);
        $activity5 = Activity::factory()->create(['user_id' => $this->user->id, 'updated_at' => now()->subDays(2)]);

        Activity::factory()->count(50)->create();
        $response = $this->getJson(route('activity.search', ['query' => null, 'perPage' => 100]));
        $response->assertJsonCount(5, 'data');

        $this->assertEquals($activity3->id, $response->json()['data'][0]['id']);
        $this->assertEquals($activity4->id, $response->json()['data'][1]['id']);
        $this->assertEquals($activity1->id, $response->json()['data'][2]['id']);
        $this->assertEquals($activity5->id, $response->json()['data'][3]['id']);
        $this->assertEquals($activity2->id, $response->json()['data'][4]['id']);
    }
}
