<?php

namespace Tests\Feature\Backup;

use App\Jobs\CreateBackup;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;

class BackupStoreTest extends TestCase
{
    /** @test */
    public function it_creates_a_sync_with_a_backup_task()
    {
        $this->authenticated();

        Bus::fake([CreateBackup::class]);

        $this->post(route('backup.store'));

        Bus::assertDispatched(CreateBackup::class, fn (CreateBackup $job) => $job->user->id === $this->user->id);
    }

    /** @test */
    public function it_redirects_to_show_all_backups()
    {
        $this->authenticated();
        Bus::fake([CreateBackup::class]);

        $response = $this->post(route('backup.store'));
        $response->assertRedirect(route('backup.index'));
    }

    /** @test */
    public function you_must_be_authenticated()
    {
        $response = $this->post(route('backup.store'));
        $response->assertRedirect(route('login'));
    }
}
