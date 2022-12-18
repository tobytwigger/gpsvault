<?php

namespace Tests\Feature\Api\Place;

use App\Models\Place;
use Tests\TestCase;

class PlaceIndexTest extends TestCase
{
    /** @test */
    public function index_loads_places_ordered_by_name()
    {
        $this->authenticatedWithSanctum();
        $places = Place::factory()->count(5)->create(['user_id' => $this->user->id]);
        $places[0]->name = 'Apple';
        $places[1]->name = 'Banana';
        $places[2]->name = 'Carrot';
        $places[3]->name = 'Grape';
        $places[4]->name = 'Avocado';
        $places->map->save();

        $this->getJson(route('api.place.index'))
            ->assertStatus(200)
            ->assertJsonPath('data.0.id', $places[0]->id)
            ->assertJsonPath('data.1.id', $places[4]->id)
            ->assertJsonPath('data.2.id', $places[1]->id)
            ->assertJsonPath('data.3.id', $places[2]->id)
            ->assertJsonPath('data.4.id', $places[3]->id);
    }

    /** @test */
    public function index_paginates_places()
    {
        $this->authenticatedWithSanctum();
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

        $this->getJson(route('api.place.index', ['page' => 2, 'perPage' => 6]))
            ->assertStatus(200)
            ->assertJsonPath('data.0.id', $places[18]->id)
            ->assertJsonPath('data.1.id', $places[20]->id)
            ->assertJsonPath('data.2.id', $places[22]->id)
            ->assertJsonPath('data.3.id', $places[21]->id)
            ->assertJsonPath('data.4.id', $places[19]->id)
            ->assertJsonPath('data.5.id', $places[9]->id);
    }

    /** @test */
    public function you_must_be_authenticated()
    {
        $this->getJson(route('api.place.index'))->assertUnauthorized();
    }
}
