<?php

namespace Tests\Feature\Stage;

use App\Models\Route;
use App\Models\Stage;
use App\Models\Tour;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class StageShowTest extends TestCase
{

    /** @test */
    public function it_shows_the_stage(){
        $this->authenticated();
        $tour = Tour::factory()->create(['id' => 123, 'user_id' => $this->user->id]);
        $route = Route::factory()->create(['id' => 500]);
        $stage = Stage::factory()->tour($tour)->route($route)->create();

        $this->get(route('tour.stage.show', [$tour, $stage]))
            ->assertInertia(fn(Assert $page) => $page
                ->component('Stage/Show')
                ->has('tour', fn (Assert $page) => $page
                    ->where('id', $tour->id)
                    ->etc()
                )
                ->has('stage', fn (Assert $page) => $page
                    ->where('id', $stage->id)
                    ->etc()
                )
                ->has('route', fn (Assert $page) => $page
                    ->where('id', $route->id)
                    ->etc())
            );
    }

    /** @test */
    public function a_404_is_returned_if_the_stage_does_not_exist(){
        $this->authenticated();
        $tour = Tour::factory()->create(['user_id' => $this->user->id]);
        $stage = Stage::factory()->tour($tour)->create();

        $response = $this->get(route('tour.stage.show', [$tour, 50000]));
        $response->assertStatus(404);
    }

    /** @test */
    public function a_404_is_returned_if_the_tour_does_not_exist(){
        $this->authenticated();
        $tour = Tour::factory()->create(['user_id' => $this->user->id]);
        $stage = Stage::factory()->tour($tour)->create();

        $response = $this->get(route('tour.stage.show', [58888, $stage]));
        $response->assertStatus(404);
    }

    /** @test */
    public function a_403_is_returned_if_you_do_not_own_the_tour(){
        $this->authenticated();
        $tour = Tour::factory()->create();
        $stage = Stage::factory()->tour($tour)->create();

        $response = $this->get(route('tour.stage.show', [$tour, $stage]));
        $response->assertStatus(403);
    }

    /** @test */
    public function a_404_response_is_returned_if_the_tour_does_not_contain_the_stage(){
        $this->authenticated();
        $tour = Tour::factory()->create(['user_id' => $this->user->id]);
        $stage = Stage::factory()->tour($tour)->create();
        $stage2 = Stage::factory()->create();

        $response = $this->get(route('tour.stage.show', [$tour, $stage]));
        $response->assertOk();

        $response = $this->get(route('tour.stage.show', [$tour, $stage2]));
        $response->assertStatus(404);
    }

    /** @test */
    public function you_must_be_authenticated(){
        $tour = Tour::factory()->create();
        $stage = Stage::factory()->create();

        $this->get(route('tour.stage.show', [$tour, $stage]))->assertRedirect(route('login'));
    }

}
