<?php

namespace Tests\Feature\Activity;

use App\Models\Activity;
use Illuminate\Support\Str;
use Tests\TestCase;

class ActivityUpdateTest extends TestCase
{

    /** @test */
    public function an_activity_can_be_updated()
    {
        $this->authenticated();
        $activity = Activity::factory()->create(['user_id' => $this->user->id, 'name' => 'Old Name', 'description' => 'Old Description']);

        $response = $this->put(route('activity.update', $activity), ['name' => 'New Name', 'description' => 'New Description']);

        $this->assertDatabaseHas('activities', [
            'id' => $activity->id, 'name' => 'New Name', 'description' => 'New Description'
        ]);
    }

    /** @test */
    public function it_redirects_to_show_the_updated_activity()
    {
        $this->authenticated();
        $activity = Activity::factory()->create(['user_id' => $this->user->id, 'name' => 'Old Name', 'description' => 'Old Description']);

        $response = $this->put(route('activity.update', $activity), ['name' => 'New Name', 'description' => 'New Description'])
            ->assertRedirect(route('activity.show', $activity));
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

        $activity = Activity::factory()->create(['user_id' => $this->user->id]);

        $response = $this->put(route('activity.update', $activity), [$key => $value]);
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
            ['description', 'This is a new description', false]
        ];
    }

    /** @test */
    public function you_must_be_authenticated()
    {
        $activity = Activity::factory()->create(['name' => 'Old Name', 'description' => 'Old Description']);

        $response = $this->put(route('activity.update', $activity), ['name' => 'New Name', 'description' => 'New Description'])
            ->assertRedirect(route('login'));
    }

    /** @test */
    public function you_can_only_update_your_own_activity()
    {
        $this->authenticated();
        $activity = Activity::factory()->create(['name' => 'Old Name', 'description' => 'Old Description']);

        $response = $this->put(route('activity.update', $activity), ['name' => 'New Name', 'description' => 'New Description'])
            ->assertStatus(403);
    }
}
