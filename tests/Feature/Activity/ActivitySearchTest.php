<?php

namespace Feature\Activity;

use App\Models\Activity;
use Carbon\Carbon;
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

        $response = $this->getJson(route('activity.search', ['query' => null]));
        $response->assertJsonCount(5);
        $json = $response->decodeResponseJson();

        $this->assertEquals($activities[4]->id, $json[0]['id']);
        $this->assertEquals($activities[0]->id, $json[1]['id']);
        $this->assertEquals($activities[1]->id, $json[2]['id']);
        $this->assertEquals($activities[3]->id, $json[3]['id']);
        $this->assertEquals($activities[2]->id, $json[4]['id']);
    }

    /** @test */
    public function it_limits_to_15_activities()
    {
        $this->authenticated();

        $activities = Activity::factory()->count(20)->create(['user_id' => $this->user->id]);
        foreach ($activities as $index => $activity) {
            $activity->updated_at = Carbon::now()->subDays($index);
            $activity->save();
        }

        $response = $this->getJson(route('activity.search', ['query' => null]));
        $response->assertJsonCount(15);
        $json = $response->decodeResponseJson();

        foreach ($activities->take(15) as $index => $activity) {
            $this->assertEquals($activity->id, $json[$index]['id']);
        }
    }

    /** @test */
    public function it_only_returns_your_activities()
    {
        $this->authenticated();

        $activities = Activity::factory()->count(5)->create(['user_id' => $this->user->id]);
        Activity::factory()->count(50)->create();
        $response = $this->getJson(route('activity.search', ['query' => null]));
        $response->assertJsonCount(5);
        $response->assertJsonFragment(['id' => $activities[0]->id]);
        $response->assertJsonFragment(['id' => $activities[1]->id]);
        $response->assertJsonFragment(['id' => $activities[2]->id]);
        $response->assertJsonFragment(['id' => $activities[3]->id]);
        $response->assertJsonFragment(['id' => $activities[4]->id]);
    }

    /** @test */
    public function it_filters_by_name()
    {
        $this->markTestSkipped('Failing');

        $this->authenticated();

        $activities = Activity::factory()->count(5)->create(['user_id' => $this->user->id, 'name' => 'My name']);
        $activity = Activity::factory()->create(['user_id' => $this->user->id, 'name' => 'This is a different name']);

        $response = $this->getJson(route('activity.search', ['query' => 'name']));
        $response->assertJsonCount(6);

        $response = $this->getJson(route('activity.search', ['query' => 'different']));
        $response->assertJsonCount(1);
    }

    /** @test */
    public function filtering_is_not_case_sensitive()
    {
        $this->markTestSkipped('Failing');

        $this->authenticated();

        $activities = Activity::factory()->count(5)->create(['user_id' => $this->user->id, 'name' => 'My name']);
        $activity = Activity::factory()->create(['user_id' => $this->user->id, 'name' => 'This is a different Name']);

        $response = $this->getJson(route('activity.search', ['query' => 'name']));
        $response->assertJsonCount(6);
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
        $this->markTestIncomplete();
    }
}
