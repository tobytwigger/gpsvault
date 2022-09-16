<?php

namespace App\Integrations\Strava\Import\Upload\Importers;

use App\Exceptions\ActivityDuplicate;
use App\Models\Activity;
use App\Models\File;
use App\Services\File\FileUploader;
use App\Services\File\Upload;
use Illuminate\Support\Str;

class ActivityImporter extends Importer
{
    protected function import()
    {
        $this->zip->contents->activityFiles()->each(function(ZipFile $filename) {
            $this->processActivityFile($filename);
        });
    }

    private function existingActivityUsingFile(ZipFile $filename): ?Activity
    {
        $uploadId = intval((string) Str::of(Str::substr($filename, 11))->before('.'));

        return Activity::linkedTo('strava')->whereAdditionalData('strava_upload_id', $uploadId)->first();
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
                    'file_location' => $filename,
                ]);

                return;
            }
            if ($activity->file()->exists()) {
                $this->failed('duplicate', [
                    'duplicate_id' => $activity->id,
                ]);

                return;
            }

            $file = $this->convertToFile($filename);

            try {
                $activity = \App\Services\ActivityImport\ActivityImporter::update($activity)
                    ->withActivityFile($file)
                    ->save();
            } catch (ActivityDuplicate $exception) {
                $this->failed('duplicate', ['duplicate_id' => $exception->activity->id]);
            }

            $this->succeeded('Activity file added to activity ' . $activity->id);
        } catch (\Exception $e) {
            $this->failed('exception', ['message' => $e->getMessage()]);

            return;
        }
    }

    public function convertToFile(ZipFile $filename): File
    {
        $contents = $this->zip->extract($filename);

        return Upload::withContents(
            trim($contents),
            $filename,
            $this->user,
            FileUploader::ARCHIVE
        );
    }

}
