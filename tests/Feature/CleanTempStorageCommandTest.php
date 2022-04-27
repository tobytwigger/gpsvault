<?php

namespace Tests\Feature;

use App\Console\Commands\CleanTempStorage;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CleanTempStorageCommandTest extends TestCase
{

    /** @test */
    public function it_removes_everything_older_than_12_hours()
    {
        Storage::fake('temp');
        Storage::disk('temp')->put('test', 'one');
        Storage::disk('temp')->put('test-two', 'two');
        Storage::disk('temp')->put('test-three', 'three');
        touch(Storage::disk('temp')->path('test'), time() - (60 * 60 * 24 * 5)); // 5 days ago
        touch(Storage::disk('temp')->path('test-two'), time() - (60 * 60 * 15)); // 15 hours ago
        touch(Storage::disk('temp')->path('test-three'), time() - (60 * 60 * 5)); // 5 hours ago

        $this->artisan(CleanTempStorage::class)
            ->expectsOutput('Removing 2 files from temp.');

        Storage::disk('temp')->assertMissing('test');
        Storage::disk('temp')->assertMissing('test-two');
        Storage::disk('temp')->assertExists('test-three');
    }
}
