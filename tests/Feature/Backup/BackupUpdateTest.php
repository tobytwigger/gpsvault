<?php

namespace Tests\Feature\Backup;

use App\Models\File;
use Illuminate\Support\Str;
use Tests\TestCase;

class BackupUpdateTest extends TestCase
{

    /** @test */
    public function an_backup_can_be_updated()
    {
        $this->authenticated();
        $backup = File::factory()->archive()->create(['user_id' => $this->user->id, 'title' => 'Old Name', 'caption' => 'Old Description']);

        $response = $this->put(route('backup.update', $backup), ['title' => 'New Name', 'caption' => 'New Description']);

        $this->assertDatabaseHas('files', [
            'id' => $backup->id, 'title' => 'New Name', 'caption' => 'New Description'
        ]);
    }

    /** @test */
    public function it_redirects_to_index()
    {
        $this->authenticated();
        $backup = File::factory()->archive()->create(['user_id' => $this->user->id, 'title' => 'Old Name', 'caption' => 'Old Description']);

        $response = $this->put(route('backup.update', $backup), ['title' => 'New Name', 'caption' => 'New Description'])
            ->assertRedirect(route('backup.index'));
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

        $backup = File::factory()->archive()->create(['user_id' => $this->user->id]);

        $response = $this->put(route('backup.update', $backup), [$key => $value]);
        if (!$error) {
            $response->assertSessionHasNoErrors();
        } else {
            $response->assertSessionHasErrors([$key => $error]);
        }
    }

    public function validationDataProvider(): array
    {
        return [
            ['title', Str::random(300), 'The title must not be greater than 255 characters.'],
            ['title', true, 'The title must be a string.'],
            ['title', 'This is a new title', false],
            ['caption', Str::random(65536), 'The caption must not be greater than 65535 characters.'],
            ['caption', true, 'The caption must be a string.'],
            ['caption', 'This is a new caption', false]
        ];
    }

    /** @test */
    public function you_must_be_authenticated()
    {
        $backup = File::factory()->archive()->create(['title' => 'Old Name', 'caption' => 'Old Description']);

        $response = $this->put(route('backup.update', $backup), ['title' => 'New Name', 'caption' => 'New Description'])
            ->assertRedirect(route('login'));
    }

    /** @test */
    public function you_can_only_update_your_own_backup()
    {
        $this->authenticated();
        $backup = File::factory()->archive()->create(['title' => 'Old Name', 'caption' => 'Old Description']);

        $response = $this->put(route('backup.update', $backup), ['title' => 'New Name', 'caption' => 'New Description'])
            ->assertStatus(403);
    }
}
