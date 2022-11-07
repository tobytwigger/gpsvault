<?php

namespace Tests\Feature\Backup;

use App\Models\File;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BackupDestroyTest extends TestCase
{
    /** @test */
    public function it_deletes_the_backup()
    {
        $this->authenticated();
        $backup = File::factory()->archive()->create(['user_id' => $this->user->id]);
        $this->assertDatabaseHas('files', ['id' => $backup->id]);

        $response = $this->delete(route('backup.destroy', $backup));
        $response->assertRedirect(route('backup.index'));

        $this->assertDatabaseMissing('files', ['id' => $backup->id]);
    }

    /** @test */
    public function it_returns_a_403_if_the_backup_is_not_owned_by_you()
    {
        $this->authenticated();
        $backup = File::factory()->archive()->create();

        $response = $this->delete(route('backup.destroy', $backup));
        $response->assertStatus(403);
    }

    /** @test */
    public function you_must_be_authenticated()
    {
        $backup = File::factory()->archive()->create();

        $this->delete(route('backup.destroy', $backup))
            ->assertRedirect(route('login'));
    }

    /** @test */
    public function it_deletes_the_file_from_storage()
    {
        $this->authenticated();

        $backup = File::factory()->archive()->create(['user_id' => $this->user->id]);
        Storage::disk('test-fake')->assertExists($backup->path);

        $response = $this->delete(route('backup.destroy', $backup));
        $response->assertRedirect(route('backup.index'));

        Storage::disk('test-fake')->assertMissing($backup->path);
    }
}
