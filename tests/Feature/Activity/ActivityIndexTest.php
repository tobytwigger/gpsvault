<?php

namespace Tests\Feature\Activity;

use App\Models\Activity;
use App\Models\Stats;
use App\Models\User;
use Carbon\Carbon;
use Tests\TestCase;
use Inertia\Testing\AssertableInertia as Assert;

class ActivityIndexTest extends TestCase
{

    /** @test */
    public function index_loads_activities_ordered_by_date(){
        $this->authenticated();
        $activities = Activity::factory()->count(5)->create(['user_id' => $this->user->id]);
        Stats::factory()->activity($activities[0])->create(['started_at' => Carbon::now()->subDays(4)]);
        Stats::factory()->activity($activities[1])->create(['started_at' => Carbon::now()->subDays(2)]);
        Stats::factory()->activity($activities[2])->create(['started_at' => Carbon::now()->subDays(1)]);
        Stats::factory()->activity($activities[3])->create(['started_at' => Carbon::now()->subDays(11)]);
        Stats::factory()->activity($activities[4])->create(['started_at' => Carbon::now()->subDays(4)]);

        $this->get(route('activity.index'))
            ->assertStatus(200)
            ->assertInertia(fn(Assert $page) => $page
                ->component('Activity/Index')
                ->has('activities', fn (Assert $page) => $page
                    ->has('data', 5)
                    ->has('data.0', fn(Assert $page) => $page->where('name', $activities[2]->name)->etc())
                    ->has('data.1', fn(Assert $page) => $page->where('id', $activities[1]->id)->etc())
                    ->has('data.2', fn(Assert $page) => $page->where('id', $activities[0]->id)->etc())
                    ->has('data.3', fn(Assert $page) => $page->where('id', $activities[4]->id)->etc())
                    ->has('data.4', fn(Assert $page) => $page->where('id', $activities[3]->id)->etc())
                    ->etc()
                )
            );
    }

    /** @test */
    public function index_paginates_activities(){
        $this->authenticated();
        $activities = Activity::factory()->count(20)->create(['user_id' => $this->user->id]);

        $this->get(route('activity.index', ['page' => 2, 'perPage' => 4]))
            ->assertStatus(200)
            ->assertInertia(fn(Assert $page) => $page
                ->component('Activity/Index')
                ->has('activities', fn (Assert $page) => $page
                    ->has('data', 4)
                    ->has('data.0', fn(Assert $page) => $page->where('name', $activities[4]->name)->etc())
                    ->has('data.1', fn(Assert $page) => $page->where('id', $activities[5]->id)->etc())
                    ->has('data.2', fn(Assert $page) => $page->where('id', $activities[6]->id)->etc())
                    ->has('data.3', fn(Assert $page) => $page->where('id', $activities[7]->id)->etc())
                    ->etc()
                )
            );
    }

    /** @test */
    public function index_only_returns_your_activities(){
        $this->authenticated();
        $activities = Activity::factory()->count(3)->create(['user_id' => $this->user->id]);
        Activity::factory()->count(2)->create();

        $this->get(route('activity.index'))
            ->assertStatus(200)
            ->assertInertia(fn(Assert $page) => $page
                ->component('Activity/Index')
                ->has('activities', fn (Assert $page) => $page
                    ->has('data', 3)
                    ->has('data.0', fn(Assert $page) => $page->where('name', $activities[0]->name)->etc())
                    ->has('data.1', fn(Assert $page) => $page->where('id', $activities[1]->id)->etc())
                    ->has('data.2', fn(Assert $page) => $page->where('id', $activities[2]->id)->etc())
                    ->etc()
                )
            );
    }

    /** @test */
    public function you_must_be_authenticated(){
        $this->get(route('activity.index'))->assertRedirect(route('login'));
    }

}
