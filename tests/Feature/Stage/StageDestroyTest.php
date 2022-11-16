<?php

namespace Tests\Feature\Stage;

use App\Models\Stage;
use App\Models\Tour;
use Tests\TestCase;

class StageDestroyTest extends TestCase
{
    /** @test */
    public function destroy_deletes_a_stage()
    {
        $this->authenticated();
        $tour = Tour::factory()->create(['user_id' => $this->user->id]);
        $stage = Stage::factory()->create(['tour_id' => $tour->id]);

        $this->assertDatabaseHas('stages', ['id' => $stage->id]);

        $response = $this->delete(route('tour.stage.destroy', [$tour, $stage]));
        $response->assertRedirect(route('tour.show', $tour));
        $this->assertDatabaseMissing('stages', ['id' => $stage->id]);
    }

    /** @test */
    public function stages_are_reordered_on_delete()
    {
        $this->authenticated();
        $tour = Tour::factory()->create(['user_id' => $this->user->id]);
        $stage1 = Stage::factory()->create(['tour_id' => $tour->id, 'stage_number' => 1]);
        $stage2 = Stage::factory()->create(['tour_id' => $tour->id, 'stage_number' => 2]);
        $stage3 = Stage::factory()->create(['tour_id' => $tour->id, 'stage_number' => 3]);
        $stage4 = Stage::factory()->create(['tour_id' => $tour->id, 'stage_number' => 4]);
        $stage5 = Stage::factory()->create(['tour_id' => $tour->id, 'stage_number' => 5]);

        $response = $this->delete(route('tour.stage.destroy', [$tour, $stage3]));
        $this->assertDatabaseMissing('stages', ['id' => $stage3->id]);

        $this->assertDatabaseHas('stages', ['id' => $stage1->id, 'stage_number' => 1]);
        $this->assertDatabaseHas('stages', ['id' => $stage2->id, 'stage_number' => 2]);
        $this->assertDatabaseHas('stages', ['id' => $stage4->id, 'stage_number' => 3]);
        $this->assertDatabaseHas('stages', ['id' => $stage5->id, 'stage_number' => 4]);
    }

    /** @test */
    public function a_404_response_is_returned_if_the_tour_does_not_contain_the_stage()
    {
        $this->authenticated();
        $tour = Tour::factory()->create(['user_id' => $this->user->id]);
        $stage = Stage::factory()->create(['tour_id' => $tour->id]);
        $stage2 = Stage::factory()->create();

        $response = $this->delete(route('tour.stage.destroy', [$tour, $stage]));
        $response->assertRedirect();

        $response = $this->delete(route('tour.stage.destroy', [$tour, $stage2]));
        $response->assertStatus(404);
    }

    /** @test */
    public function a_403_is_returned_if_you_do_not_own_the_tour()
    {
        $this->authenticated();
        $tour = Tour::factory()->create();
        $stage = Stage::factory()->create(['tour_id' => $tour->id]);

        $response = $this->delete(route('tour.stage.destroy', [$tour, $stage]));
        $response->assertStatus(403);
    }

    /** @test */
    public function you_must_be_authenticated()
    {
        $tour = Tour::factory()->create();
        $stage = Stage::factory()->create(['tour_id' => $tour->id]);

        $this->delete(route('tour.stage.destroy', [$tour, $stage]))->assertRedirect(route('login'));
    }
}
