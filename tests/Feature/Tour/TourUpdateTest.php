<?php

namespace Tests\Feature\Tour;

use App\Models\Tour;
use Illuminate\Support\Str;
use Tests\TestCase;

class TourUpdateTest extends TestCase
{

    /** @test */
    public function an_tour_can_be_updated()
    {
        $this->authenticated();
        $tour = Tour::factory()->create(['user_id' => $this->user->id, 'name' => 'Old Name', 'description' => 'Old Description', 'notes' => 'Old Notes']);

        $response = $this->put(route('tour.update', $tour), ['name' => 'New Name', 'description' => 'New Description', 'notes' => 'New Notes']);

        $this->assertDatabaseHas('tours', [
            'id' => $tour->id, 'name' => 'New Name', 'description' => 'New Description', 'notes' => 'New Notes'
        ]);
    }

    /** @test */
    public function it_redirects_to_show_the_updated_tour()
    {
        $this->authenticated();
        $tour = Tour::factory()->create(['user_id' => $this->user->id, 'name' => 'Old Name']);

        $response = $this->put(route('tour.update', $tour), ['name' => 'New Name'])
            ->assertRedirect(route('tour.show', $tour));
    }

    /**
     * @test
     * @dataProvider validationDataProvider
     * @param mixed $key
     * @param mixed $value
     * @param mixed $error
     */
    public function it_validates($key, $value, $error)
    {
        $this->authenticated();

        $tour = Tour::factory()->create(['user_id' => $this->user->id]);

        $response = $this->put(route('tour.update', $tour), [$key => $value]);
        if (!$error) {
            $response->assertSessionHasNoErrors();
        } else {
            $response->assertSessionHasErrors([$key => $error]);
        }
    }

    public function validationDataProvider(): array
    {
        return [
            ['name', Str::random(300), 'The name must not be greater than 255 characters.'],
            ['name', true, 'The name must be a string.'],
            ['name', 'This is a new name', false],
            ['description', Str::random(65536), 'The description must not be greater than 65535 characters.'],
            ['description', true, 'The description must be a string.'],
            ['description', 'This is a new description', false],
            ['notes', Str::random(65536), 'The notes must not be greater than 65535 characters.'],
            ['notes', true, 'The notes must be a string.'],
            ['notes', 'This is a new notes', false],
        ];
    }

    /** @test */
    public function you_must_be_authenticated()
    {
        $tour = Tour::factory()->create(['name' => 'Old Name']);

        $response = $this->put(route('tour.update', $tour), ['name' => 'New Name'])
            ->assertRedirect(route('login'));
    }

    /** @test */
    public function you_can_only_update_your_own_tour()
    {
        $this->authenticated();
        $tour = Tour::factory()->create(['name' => 'Old Name']);

        $response = $this->put(route('tour.update', $tour), ['name' => 'New Name'])
            ->assertStatus(403);
    }
}
