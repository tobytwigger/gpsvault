<?php

namespace Tests\Feature\Api\Backup;

use App\Models\File;
use Carbon\Carbon;
use Tests\TestCase;

class BackupIndexTest extends TestCase
{
    /** @test */
    public function index_loads_backups_ordered_by_date()
    {
        $this->authenticatedWithSanctum();
        $backups = File::factory()->archive()->count(5)->create(['user_id' => $this->user->id]);
        $backups[0]->created_at = Carbon::now()->subDays(4);
        $backups[1]->created_at = Carbon::now()->subDays(2);
        $backups[2]->created_at = Carbon::now()->subDays(1);
        $backups[3]->created_at = Carbon::now()->subDays(11);
        $backups[4]->created_at = Carbon::now()->subDays(4)->subMinute(1);
        $backups->map->save();

        $this->getJson(route('api.backup.index'))
            ->assertStatus(200)
            ->assertJsonPath('data.0.id', $backups[2]->id)
            ->assertJsonPath('data.1.id', $backups[1]->id)
            ->assertJsonPath('data.2.id', $backups[0]->id)
            ->assertJsonPath('data.3.id', $backups[4]->id)
            ->assertJsonPath('data.4.id', $backups[3]->id);
    }

    /** @test */
    public function index_paginates_backups()
    {
        $this->authenticatedWithSanctum();
        $backups = File::factory()->archive()->count(20)->create(['user_id' => $this->user->id, 'created_at' => null]);
        foreach ($backups as $index => $backup) {
            $backup->created_at = Carbon::now()->subDays($index);
            $backup->save();
        }

        $this->getJson(route('api.backup.index', ['page' => 2, 'perPage' => 4]))
            ->assertStatus(200)
            ->assertJsonPath('data.0.id', $backups[4]->id)
            ->assertJsonPath('data.1.id', $backups[5]->id)
            ->assertJsonPath('data.2.id', $backups[6]->id)
            ->assertJsonPath('data.3.id', $backups[7]->id);
    }

    /** @test */
    public function index_only_returns_your_backups()
    {
        $this->authenticatedWithSanctum();
        $backups = File::factory()->archive()->count(3)->create(['user_id' => $this->user->id, 'created_at' => null]);
        File::factory()->archive()->count(2)->create(['created_at' => null]);

        $this->getJson(route('api.backup.index'))
            ->assertStatus(200)
            ->assertJsonPath('data.0.id', $backups[0]->id)
            ->assertJsonPath('data.1.id', $backups[1]->id)
            ->assertJsonPath('data.2.id', $backups[2]->id);
    }

    /** @test */
    public function you_must_be_authenticated()
    {
        $this->getJson(route('api.backup.index'))->assertUnauthorized();
    }
}
