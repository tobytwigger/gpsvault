<?php

namespace Tests\Feature\Tour;

use App\Models\Tour;
use App\Models\User;
use Carbon\Carbon;
use Inertia\Testing\Assert;
use Tests\TestCase;

class TourIndexTest extends TestCase
{

    /** @test */
    public function index_loads_tours_ordered_by_date(){
        $this->authenticated();
        $tours = Tour::factory()->count(5)->create(['user_id' => $this->user->id]);
        $tours[0]->created_at = Carbon::now()->subDays(4);
        $tours[1]->created_at = Carbon::now()->subDays(2);
        $tours[2]->created_at = Carbon::now()->subDays(1);
        $tours[3]->created_at = Carbon::now()->subDays(11);
        $tours[4]->created_at = Carbon::now()->subDays(4);
        $tours->map->save();

        $this->get(route('tour.index'))
            ->assertStatus(200)
            ->assertInertia(fn(Assert $page) => $page
                ->component('Tour/Index')
                ->has('tours', fn (Assert $page) => $page
                    ->has('data', 5)
                    ->has('data.0', fn(Assert $page) => $page->where('name', $tours[2]->name)->etc())
                    ->has('data.1', fn(Assert $page) => $page->where('id', $tours[1]->id)->etc())
                    ->has('data.2', fn(Assert $page) => $page->where('id', $tours[0]->id)->etc())
                    ->has('data.3', fn(Assert $page) => $page->where('id', $tours[4]->id)->etc())
                    ->has('data.4', fn(Assert $page) => $page->where('id', $tours[3]->id)->etc())
                    ->etc()
                )
            );
    }

    /** @test */
    public function index_paginates_tours(){
        $this->authenticated();
        $tours = Tour::factory()->count(20)->create(['user_id' => $this->user->id, 'created_at' => null]);

        $this->get(route('tour.index', ['page' => 2, 'perPage' => 4]))
            ->assertStatus(200)
            ->assertInertia(fn(Assert $page) => $page
                ->component('Tour/Index')
                ->has('tours', fn (Assert $page) => $page
                    ->has('data', 4)
                    ->has('data.0', fn(Assert $page) => $page->where('name', $tours[4]->name)->etc())
                    ->has('data.1', fn(Assert $page) => $page->where('id', $tours[5]->id)->etc())
                    ->has('data.2', fn(Assert $page) => $page->where('id', $tours[6]->id)->etc())
                    ->has('data.3', fn(Assert $page) => $page->where('id', $tours[7]->id)->etc())
                    ->etc()
                )
            );
    }

    /** @test */
    public function index_only_returns_your_tours(){
        $this->authenticated();
        $tours = Tour::factory()->count(3)->create(['user_id' => $this->user->id, 'created_at' => null]);
        Tour::factory()->count(2)->create(['created_at' => null]);

        $this->get(route('tour.index'))
            ->assertStatus(200)
            ->assertInertia(fn(Assert $page) => $page
                ->component('Tour/Index')
                ->has('tours', fn (Assert $page) => $page
                    ->has('data', 3)
                    ->has('data.0', fn(Assert $page) => $page->where('name', $tours[0]->name)->etc())
                    ->has('data.1', fn(Assert $page) => $page->where('id', $tours[1]->id)->etc())
                    ->has('data.2', fn(Assert $page) => $page->where('id', $tours[2]->id)->etc())
                    ->etc()
                )
            );
    }

    /** @test */
    public function you_must_be_authenticated(){
        $this->get(route('tour.index'))->assertRedirect(route('login'));
    }

}
