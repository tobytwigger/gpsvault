<?php

namespace App\Integrations\Strava\Tasks;

use App\Integrations\Strava\Client\Strava;
use App\Integrations\Strava\Import\Importer;
use App\Integrations\Strava\Import\Importers\ImportingZip;
use App\Models\User;
use App\Services\Sync\Task;
use Illuminate\Support\Facades\Storage;

class StravaUpload extends Task
{
    private Strava $strava;

    public function __construct(Strava $strava)
    {
        $this->strava = $strava;
    }

    public function description(): string
    {
        return 'Upload all the information stored in Strava to compliment that retrieved via the API.';
    }

    public function name(): string
    {
        return 'Full Strava upload';
    }

    public function setupComponent(): ?string
    {
        return 'task-strava-upload';
    }

    public function isChecked(User $user): bool
    {
        return false;
    }

    public function requiredConfig(): array
    {
        return ['file'];
    }

    public function validationRules(): array
    {
        return [
            'file' => 'required|array|min:1|max:1',
            'file.*' => 'file|mimes:zip'
        ];
    }

    public function processConfig(array $config): array
    {
        $path = $config['file'][0]->store('strava_archives', 'temp');
        $config['file_path'] = $path;
        unset($config['file']);

        return $config;
    }

    public function run()
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

    public function runsAfter(): array
    {
        return [
            SaveAllActivities::id()
        ];
    }
}
