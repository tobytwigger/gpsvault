<?php

namespace Tests\Feature\Stage;

use App\Models\Stage;
use App\Models\Tour;
use Inertia\Testing\Assert;
use Tests\TestCase;

class StageStoreTest extends TestCase
{

    /** @test */
    public function it_creates_a_stage_and_shows_the_tour(){
        $this->authenticated();
        $tour = Tour::factory()->create(['user_id' => $this->user->id]);

        $this->assertDatabaseCount('stages', 0);

        $response = $this->post(route('tour.stage.store', $tour->id));
        $this->assertDatabaseCount('stages', 1);
        $stage = Stage::first();
        $response->assertRedirect(route('tour.show', $tour));
    }

    /** @test */
    public function ordering_is_sorted_and_the_new_stage_is_pushed_to_the_end(){
        $this->authenticated();

        $tour = Tour::factory()->create(['user_id' => $this->user->id]);
        $stage1 = Stage::factory()->create(['tour_id' => $tour->id, 'stage_number' => 1]);
        $stage2 = Stage::factory()->create(['tour_id' => $tour->id, 'stage_number' => 2]);
        $stage3 = Stage::factory()->create(['tour_id' => $tour->id, 'stage_number' => 4]);
        $stage4 = Stage::factory()->create(['tour_id' => $tour->id, 'stage_number' => 7]);

        $response = $this->post(route('tour.stage.store', $tour->id));
        $this->assertDatabaseCount('stages', 5);

        $this->assertDatabaseHas('stages', ['id' => $stage1->id, 'stage_number' => 1]);
        $this->assertDatabaseHas('stages', ['id' => $stage2->id, 'stage_number' => 2]);
        $this->assertDatabaseHas('stages', ['id' => $stage3->id, 'stage_number' => 3]);
        $this->assertDatabaseHas('stages', ['id' => $stage4->id, 'stage_number' => 4]);
        $this->assertDatabaseHas('stages', ['stage_number' => 5]);
    }

    /** @test */
    public function a_403_is_returned_if_you_do_not_own_the_tour(){
        $this->authenticated();
        $tour = Tour::factory()->create();

        $response = $this->post(route('tour.stage.store', [$tour]));
        $response->assertStatus(403);
    }

    /** @test */
    public function you_must_be_authenticated(){
        $tour = Tour::factory()->create();

        $this->post(route('tour.stage.store', [$tour]))->assertRedirect(route('login'));
    }

}
