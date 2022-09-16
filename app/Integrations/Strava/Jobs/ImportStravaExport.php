<?php

namespace App\Integrations\Strava\Jobs;

use App\Integrations\Strava\Import\Upload\Importer;
use App\Integrations\Strava\Import\Upload\Importers\ImportZip;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use JobStatus\Trackable;

class ImportStravaExport
{

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Trackable;

    private User $user;
    private string $zipPath;

    public function __construct(User $user, string $zipPath)
    {
        $this->user = $user;
        $this->zipPath = $zipPath;
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

        Importer::import(
            ImportZip::fromTempArchivePath($this->zipPath),
            $this->user
        );

        Storage::disk('temp')->delete($this->zipPath);

        $this->successMessage(sprintf('Importing complete.'));
    }

}
