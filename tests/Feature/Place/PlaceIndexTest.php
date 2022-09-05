<?php

namespace Tests\Feature\Place;

use App\Models\Place;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class PlaceIndexTest extends TestCase
{
    /** @test */
    public function index_loads_places_ordered_by_name()
    {
        $this->authenticated();
        $places = Place::factory()->count(5)->create(['user_id' => $this->user->id]);
        $places[0]->name = 'Apple';
        $places[1]->name = 'Banana';
        $places[2]->name = 'Carrot';
        $places[3]->name = 'Grape';
        $places[4]->name = 'Avocado';
        $places->map->save();

        $this->get(route('place.index'))
            ->assertStatus(200)
            ->assertInertia(
                fn (Assert $page) => $page
                    ->component('Place/Index')
                    ->has(
                        'places',
                        fn (Assert $page) => $page
                            ->has('data', 5)
                            ->has('data.0', fn (Assert $page) => $page->where('name', $places[0]->name)->etc())
                            ->has('data.1', fn (Assert $page) => $page->where('id', $places[4]->id)->etc())
                            ->has('data.2', fn (Assert $page) => $page->where('id', $places[1]->id)->etc())
                            ->has('data.3', fn (Assert $page) => $page->where('id', $places[2]->id)->etc())
                            ->has('data.4', fn (Assert $page) => $page->where('id', $places[3]->id)->etc())
                            ->etc()
                    )
            );
    }

    /** @test */
    public function index_paginates_places()
    {
        $this->authenticated();
        $places = [
            Place::factory()->create(['name' => 'Atest']),
            Place::factory()->create(['name' => 'Btest']),
            Place::factory()->create(['name' => 'Etest']),
            Place::factory()->create(['name' => 'Ntest']),
            Place::factory()->create(['user_id' => $this->user->id, 'name' => 'Utest']),
            Place::factory()->create(['user_id' => $this->user->id, 'name' => 'Ptest']),
            Place::factory()->create(['user_id' => $this->user->id, 'name' => 'Ttest']),
            Place::factory()->create(['name' => 'Dtest']),
            Place::factory()->create(['name' => 'Mtest']),
            Place::factory()->create(['name' => 'Ltest']),
            Place::factory()->create(['name' => 'Rtest']),
            Place::factory()->create(['name' => 'Otest']),
            Place::factory()->create(['name' => 'Ftest']),
            Place::factory()->create(['user_id' => $this->user->id, 'name' => 'Ctest']),
            Place::factory()->create(['user_id' => $this->user->id, 'name' => 'Vtest']),
            Place::factory()->create(['user_id' => $this->user->id, 'name' => 'Qtest']),
            Place::factory()->create(['user_id' => $this->user->id, 'name' => 'Stest']),
            Place::factory()->create(['user_id' => $this->user->id, 'name' => 'Wtest']),
            Place::factory()->create(['user_id' => $this->user->id, 'name' => 'Gtest']),
            Place::factory()->create(['user_id' => $this->user->id, 'name' => 'Ktest']),
            Place::factory()->create(['user_id' => $this->user->id, 'name' => 'Htest']),
            Place::factory()->create(['user_id' => $this->user->id, 'name' => 'Jtest']),
            Place::factory()->create(['user_id' => $this->user->id, 'name' => 'Itest']),
        ];

        $this->get(route('place.index', ['page' => 2, 'perPage' => 6]))
            ->assertStatus(200)
            ->assertInertia(
                fn (Assert $page) => $page
                    ->component('Place/Index')
                    ->has(
                        'places',
                        fn (Assert $page) => $page
                            ->has('data', 6)
                            ->has('data.0', fn (Assert $page) => $page->where('name', $places[18]->name)->etc())
                            ->has('data.1', fn (Assert $page) => $page->where('name', $places[20]->name)->etc())
                            ->has('data.2', fn (Assert $page) => $page->where('name', $places[22]->name)->etc())
                            ->has('data.3', fn (Assert $page) => $page->where('name', $places[21]->name)->etc())
                            ->has('data.4', fn (Assert $page) => $page->where('name', $places[19]->name)->etc())
                            ->has('data.5', fn (Assert $page) => $page->where('name', $places[9]->name)->etc())
                            ->etc()
                    )
            );
    }

    /** @test */
    public function you_must_be_authenticated()
    {
        $this->get(route('place.index'))->assertRedirect(route('login'));
    }
}
