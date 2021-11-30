<?php

namespace App\Integrations\Strava\Tasks;

use Alchemy\Zippy\Archive\MemberInterface;
use Alchemy\Zippy\Zippy;
use App\Integrations\Strava\Client\Strava;
use App\Models\Activity;
use App\Models\File;
use App\Models\User;
use App\Services\File\FileUploader;
use App\Services\File\Upload;
use App\Services\Sync\Task;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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
        $path = $config['file'][0]->store('archives', 'temp');
        $config['file_path'] = $path;
        unset($config['file']);
        return $config;
    }

    const SUCCESS = 'success';

    const UNMATCHED = 'unmatched';

    const ALREADY_SAVED = 'already_saved';

    public function run()
    {
        $this->line('Extracting Strava archive');

        // Save the zip file locally
        $archivePath = $this->config('file_path');
        $zipFile = Zippy::load()->open(Storage::disk('temp')->path($archivePath));

        // Get the entries to extract for the activities
        $entries = collect($zipFile->getMembers())
            ->filter(fn(MemberInterface $member) => Str::startsWith($member->getLocation(), 'activities/') && Str::length($member->getLocation()) > 11);

        // Extract the zip file
        $newDirectoryPath = sprintf('extracted/%s', Str::random(10));
        Storage::disk('temp')->put($newDirectoryPath . '/cycle_store_test.txt', 'test');
        $extractTo = dirname(
            Storage::disk('temp')->path($newDirectoryPath . '/cycle_store_test.txt')
        );
        $zipFile->extractMembers($entries->all(), $extractTo);

        // Upload each entry
        $entryKeys = $entries->map(fn(MemberInterface $member) => Str::substr($member->getLocation(), 11))->values();
        $successes = [];
        for($i=0;$i<$entryKeys->count();$i++) {
            if($i%10 === 0) {
                $this->line(sprintf('Extracting %u/%u activities', $i, $entryKeys->count()));
            }
            $successes[$entryKeys[$i]] = $this->processEntry($entryKeys[$i], $newDirectoryPath);
        }

        $this->line(sprintf('Cleaning up'));

        Storage::disk('temp')->deleteDirectory($newDirectoryPath);
        Storage::disk('temp')->delete($archivePath);

        $this->succeed($this->formatSuccesses($successes));

    }

    private function processEntry(string $entry, string $directory): string
    {
        try {
            // Get the activity that this file relates to
            $uploadId = Str::of($entry)->before('.');
            try {
                $activity = Activity::linkedTo('strava')->whereAdditionalDataContains('upload_id', $uploadId)->firstOrFail();
            } catch (ModelNotFoundException $e) {
                return static::UNMATCHED;
            }

            if($activity->activityFile()->exists()) {
                return static::ALREADY_SAVED;
            }

            // If file is a tar, then unzip it
            $locationInTemp = sprintf('%s/activities/%s', $directory, $entry);
            $locationInTemp = $this->isTar($locationInTemp) ? $this->unzip($locationInTemp) : $locationInTemp;

            $file = Upload::withContents(
                trim(file_get_contents($locationInTemp)),
                $this->isTar($entry) ? Str::replace('.gz', '', $entry) : $entry,
                $this->user(),
                FileUploader::ARCHIVE
            );

            $activity->activity_file_id = $file->id;
            $activity->save();

            return static::SUCCESS;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    private function isTar(string $fileName): bool
    {
        return Str::endsWith($fileName, '.gz');
    }

    public function runsAfter(): array
    {
        return [
            SaveNewActivities::id()
        ];
    }

    private function unzip(string $fileName): string
    {
        $path = Storage::disk('temp')->path($fileName);

        $bufferSize = 4096; // read 4kb at a time
        $outputFileName = str_replace('.gz', '', $path);

        // Open the file
        $file = gzopen($path, 'rb');
        $outputFile = fopen($outputFileName, 'wb');

        while (!gzeof($file)) {
            // Read buffer-size bytes
            // Both fwrite and gzread and binary-safe
            fwrite($outputFile, gzread($file, $bufferSize));
        }

        fclose($outputFile);
        gzclose($file);

        return $outputFileName;
    }

    private function formatSuccesses(array $successes): string
    {
        return sprintf(
            'Matched %u/%u files, of which %u were already saved.',
            collect($successes)->filter(fn($status) => $status === static::SUCCESS || $status === static::ALREADY_SAVED)->count(),
            collect($successes)->count(),
            collect($successes)->filter(fn($status) => $status === static::ALREADY_SAVED)->count()
        );
    }
}
