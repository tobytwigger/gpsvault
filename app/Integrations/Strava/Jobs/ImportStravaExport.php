<?php

namespace App\Integrations\Strava\Jobs;

use App\Integrations\Strava\Import\Upload\Importer;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use JobStatus\Trackable;

class ImportStravaExport implements ShouldQueue, ShouldBeUnique
{

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Trackable;

    private User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function uniqueId()
    {
        return $this->user->id;
    }

    public function alias(): ?string
    {
        return 'import-strava-export';
    }

//    public function processConfig(array $config): array
//    {
//        $path = $config['file'][0]->store('strava_archives', 'temp');
//        $config['file_path'] = $path;
//        unset($config['file']);
//
//        return $config;
//    }

    public function handle()
    {
        $this->line('Extracting Strava archive');

        $import = Importer::import(
            ImportingZip::fromTempArchivePath($this->config('file_path')),
            $this->task,
            $this->user()
        );

        Storage::disk('temp')->delete($this->config('file_path'));

        $this->succeed(sprintf('Importing complete. You may view the results at %s', url()->route('import.show', $import)));
    }

}
