<?php

namespace App\Integrations\Strava\Import\Upload\Importers;

use App\Integrations\Strava\Import\Upload\Zip\ZipFile;
use App\Jobs\AnalyseActivityFile;
use App\Models\Activity;
use App\Services\File\FileUploader;

class ActivityImporter extends Importer
{
    private array $activityIdLookup = [];

    protected function import()
    {
        $this->activityIdLookup = collect($this->zip->getCsv('activities.csv'))
            ->mapWithKeys(fn (array $entry) => [
                $entry[11] => $entry[0],
            ])
            ->all();

        $this->zip->contents->activityFiles()->each(function (ZipFile $filename) {
            $this->processActivityFile($filename);
        });
    }

    private function existingActivityUsingFile(ZipFile $filename): ?Activity
    {
        // Filename is strava_upload_id, activity is strava_id
        if (array_key_exists($filename->getFilename(), $this->activityIdLookup)) {
            $stravaId = intval($this->activityIdLookup[$filename->getFilename()]);

            return Activity::whereAdditionalData('strava_id', $stravaId)->first();
        }

        return null;
    }

    public function type(): string
    {
        return 'activities';
    }

    private function processActivityFile(ZipFile $filename)
    {
        try {
            $activity = $this->existingActivityUsingFile($filename);

            if ($activity === null) {
                $this->failed('unmatched', [
                    'file_location' => $filename->getFilename(),
                ]);

                return;
            }

            if ($activity->file()->exists()) {
                $this->failed('duplicate', [
                    'duplicate_id' => $activity->id,
                    'activity_name' => $activity->name,
                ]);

                return;
            }

            $file = $this->convertToFile($filename, FileUploader::ACTIVITY_FILE);

            $activity = \App\Services\ActivityImport\ActivityImporter::update($activity)
                ->withActivityFile($file)
                ->save();

            AnalyseActivityFile::dispatch($activity);

            $this->succeeded('imported', ['activity_id' => $activity->id, 'activity_name' => $activity->name]);
        } catch (\Exception $e) {
            $this->failed('exception', ['message' => $e->getMessage(), 'filename' => (string) $filename]);

            return;
        }
    }
}
