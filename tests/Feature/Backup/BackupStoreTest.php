<?php

namespace Tests\Feature\Backup;

use App\Models\Activity;
use App\Models\File;
use App\Services\Sync\RunSyncTask;
use App\Services\Sync\Sync;
use App\Services\Sync\SyncTask;
use App\Tasks\CreateBackupTask;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class BackupStoreTest extends TestCase
{

    /** @test */
    public function it_creates_a_sync_with_a_backup_task(){
        $this->authenticated();

        Bus::fake([RunSyncTask::class]);

        $this->post(route('backup.store'));

        Bus::assertDispatched(RunSyncTask::class, fn(RunSyncTask $job) => $job->task->taskId() === CreateBackupTask::id());
        $this->assertDatabaseCount('syncs', 1);
    }

    /** @test */
    public function it_redirects_to_show_all_backups(){
        $this->authenticated();
        Bus::fake([RunSyncTask::class]);

        $response = $this->post(route('backup.store'));
        $response->assertRedirect(route('backup.index'));
    }

    /** @test */
    public function it_throws_an_exception_if_a_sync_exists_with_a_pending_backup_task(){
        $this->authenticated();
        Bus::fake([RunSyncTask::class]);
        $sync = Sync::factory()->create(['user_id' => $this->user->id]);
        SyncTask::factory()->create(['sync_id' => $sync->id, 'task_id' => CreateBackupTask::id(), 'status' => 'queued']);

        $response = $this->post(route('backup.store'));
        $response->assertStatus(400);
    }

    /** @test */
    public function it_passes_and_redirects_if_a_sync_exists_with_a_complete_backup_task(){
        $this->authenticated();
        Bus::fake([RunSyncTask::class]);
        $sync = Sync::factory()->create(['user_id' => $this->user->id]);
        SyncTask::factory()->create(['sync_id' => $sync->id, 'task_id' => CreateBackupTask::id(), 'status' => 'cancelled']);

        $response = $this->post(route('backup.store'));
        $response->assertRedirect();
        $this->assertDatabaseCount('syncs', 2);

    }

    /** @test */
    public function you_must_be_authenticated(){
        $response = $this->post(route('backup.store'));
        $response->assertRedirect(route('login'));
    }

}
