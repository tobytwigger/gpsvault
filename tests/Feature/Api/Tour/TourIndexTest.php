<?php

namespace Tests\Feature\Api\Tour;

use App\Models\Tour;
use Carbon\Carbon;
use Tests\TestCase;

class TourIndexTest extends TestCase
{
    /** @test */
    public function index_loads_tours_ordered_by_date()
    {
        $this->authenticatedWithSanctum();
        $tours = Tour::factory()->count(5)->create(['user_id' => $this->user->id]);
        $tours[0]->created_at = Carbon::now()->subDays(4);
        $tours[1]->created_at = Carbon::now()->subDays(2);
        $tours[2]->created_at = Carbon::now()->subDays(1);
        $tours[3]->created_at = Carbon::now()->subDays(11);
        $tours[4]->created_at = Carbon::now()->subDays(4);
        $tours->map->save();

        $this->getJson(route('api.tour.index'))
            ->assertStatus(200)
            ->assertJsonPath('data.0.id', $tours[2]->id)
            ->assertJsonPath('data.1.id', $tours[1]->id)
            ->assertJsonPath('data.2.id', $tours[0]->id)
            ->assertJsonPath('data.3.id', $tours[4]->id)
            ->assertJsonPath('data.4.id', $tours[3]->id);
    }

    /** @test */
    public function index_paginates_tours()
    {
        $this->authenticatedWithSanctum();
        $tours = Tour::factory()->count(20)->create(['user_id' => $this->user->id, 'created_at' => null]);
        foreach ($tours as $index => $tour) {
            $tour->created_at = Carbon::now()->subDays($index);
            $tour->save();
        }

        $this->getJson(route('api.tour.index', ['page' => 2, 'perPage' => 4]))
            ->assertStatus(200)
            ->assertJsonPath('data.0.id', $tours[4]->id)
            ->assertJsonPath('data.1.id', $tours[5]->id)
            ->assertJsonPath('data.2.id', $tours[6]->id)
            ->assertJsonPath('data.3.id', $tours[7]->id);
    }

    /** @test */
    public function index_only_returns_your_tours()
    {
        $this->authenticatedWithSanctum();
        $tours = Tour::factory()->count(3)->create(['user_id' => $this->user->id, 'created_at' => null]);
        Tour::factory()->count(2)->create(['created_at' => null]);

        $this->getJson(route('api.tour.index'))
            ->assertStatus(200)
            ->assertJsonPath('data.0.id', $tours[0]->id)
            ->assertJsonPath('data.1.id', $tours[1]->id)
            ->assertJsonPath('data.2.id', $tours[2]->id);
    }

    /** @test */
    public function you_must_be_authenticated()
    {
        $this->getJson(route('api.tour.index'))->assertUnauthorized();
    }
}
