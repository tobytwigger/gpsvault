<?php

namespace Tests\Feature\Api\Activity;

use App\Models\Activity;
use App\Models\Stats;
use Carbon\Carbon;
use Tests\TestCase;

class IndexActivityTest extends TestCase
{
    /** @test */
    public function index_loads_activities_ordered_by_date()
    {
        $this->authenticatedWithSanctum();
        $activities = Activity::factory()->count(5)->create(['user_id' => $this->user->id]);
        Stats::factory()->activity($activities[0])->create(['started_at' => Carbon::now()->subDays(4)]);
        Stats::factory()->activity($activities[1])->create(['started_at' => Carbon::now()->subDays(2)]);
        Stats::factory()->activity($activities[2])->create(['started_at' => Carbon::now()->subDays(1)]);
        Stats::factory()->activity($activities[3])->create(['started_at' => Carbon::now()->subDays(11)]);
        Stats::factory()->activity($activities[4])->create(['started_at' => Carbon::now()->subDays(4)]);

        $this->getJson(route('api.activity.index'))
            ->assertStatus(200)
            ->assertJsonPath('data.0.id', $activities[2]->id)
            ->assertJsonPath('data.1.id', $activities[1]->id)
            ->assertJsonPath('data.2.id', $activities[0]->id)
            ->assertJsonPath('data.3.id', $activities[4]->id)
            ->assertJsonPath('data.4.id', $activities[3]->id);
    }

    /** @test */
    public function index_paginates_activities()
    {
        $this->authenticatedWithSanctum();
        $activities = Activity::factory()->count(20)->create(['user_id' => $this->user->id]);

        $this->getJson(route('api.activity.index', ['page' => 2, 'perPage' => 4]))
            ->assertStatus(200)
            ->assertJsonPath('data.0.id', $activities[4]->id)
            ->assertJsonPath('data.1.id', $activities[5]->id)
            ->assertJsonPath('data.2.id', $activities[6]->id)
            ->assertJsonPath('data.3.id', $activities[7]->id);
    }

    /** @test */
    public function index_only_returns_your_activities()
    {
        $this->authenticatedWithSanctum();
        $activities = Activity::factory()->count(3)->create(['user_id' => $this->user->id]);
        Activity::factory()->count(2)->create();

        $this->getJson(route('api.activity.index'))
            ->assertStatus(200)
            ->assertJsonPath('data.0.id', $activities[0]->id)
            ->assertJsonPath('data.1.id', $activities[1]->id)
            ->assertJsonPath('data.2.id', $activities[2]->id);
    }

    /** @test */
    public function you_must_be_authenticated()
    {
        $this->getJson(route('api.activity.index'))->assertUnauthorized();
    }
}
