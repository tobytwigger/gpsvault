<?php

namespace Tests\Feature\Backup;

use App\Models\File;
use App\Services\Sync\Sync;
use App\Services\Sync\SyncTask;
use App\Tasks\CreateBackupTask;
use Illuminate\Support\Str;
use Tests\TestCase;

class CancelBackupTest extends TestCase
{

    /** @test */
    public function a_backup_can_be_cancelled(){
        $this->authenticated();
        $sync = Sync::factory()->create(['user_id' => $this->user->id]);
        $task = SyncTask::factory()->create(['sync_id' => $sync->id, 'task_id' => CreateBackupTask::id(), 'status' => 'processing']);

        $response = $this->post(route('backup.sync.cancel', $sync));

        $this->assertEquals('cancelled', $task->refresh()->status);
    }

    /** @test */
    public function it_redirects_to_index(){
        $this->authenticated();
        $sync = Sync::factory()->create(['user_id' => $this->user->id]);
        $task = SyncTask::factory()->create(['sync_id' => $sync->id, 'task_id' => CreateBackupTask::id(), 'status' => 'processing']);

        $response = $this->post(route('backup.sync.cancel', $sync))
            ->assertRedirect(route('backup.index'));
    }

    /** @test */
    public function if_the_sync_is_not_in_progress_it_does_nothing(){
        $this->authenticated();
        $sync = Sync::factory()->create(['user_id' => $this->user->id]);
        $task = SyncTask::factory()->create(['sync_id' => $sync->id, 'task_id' => CreateBackupTask::id(), 'status' => 'succeeded']);

        $response = $this->post(route('backup.sync.cancel', $sync))
            ->assertRedirect(route('backup.index'));

        $this->assertEquals('succeeded', $task->refresh()->status);
    }

    /** @test */
    public function you_must_be_authenticated(){
        $sync = Sync::factory()->create();
        $task = SyncTask::factory()->create(['sync_id' => $sync->id, 'task_id' => CreateBackupTask::id(), 'status' => 'succeeded']);

        $response = $this->post(route('backup.sync.cancel', $sync))
            ->assertRedirect(route('login'));
    }

    /** @test */
    public function you_can_only_cancel_your_own_backup(){
        $this->authenticated();
        $sync = Sync::factory()->create();
        $task = SyncTask::factory()->create(['sync_id' => $sync->id, 'task_id' => CreateBackupTask::id(), 'status' => 'succeeded']);

        $response = $this->post(route('backup.sync.cancel', $sync))
            ->assertStatus(403);
    }
}
