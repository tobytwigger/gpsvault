<?php

namespace App\Integrations\Strava\Import\Upload\Importers;

use App\Integrations\Strava\Import\Upload\Zip\ZipFile;
use App\Models\Activity;
use App\Models\File;
use App\Services\File\FileUploader;
use App\Services\File\Upload;
use Illuminate\Support\Str;

class PhotoImporter extends Importer
{
    private array $captions = [];

    protected function import()
    {
        $this->captions =  collect($this->zip->getCsv('photos.csv'))
            ->mapWithKeys(fn(array $entry) => [
                $entry[0] => $entry[1]
            ])
            ->all();

        $this->zip->contents->photos()->each(function(ZipFile $filename) {
            $this->processFile($filename);
        });
    }

    private function processFile(ZipFile $file)
    {

        try {
            $activity = $this->getActivity($file);

            if($this->photoAlreadyImported($file) === true) {
                $this->failed('duplicate', [
                    'file_location' => (string) $file,
                    'activity_id' => $activity->id,
                    'activity_name' => $activity->name
                ]);
                return;
            }



            if ($activity === null) {
                $this->failed('unmatched', [
                    'file_location' => $file->getFilename(),
                ]);

                return;
            }

            $uploadedFile = $this->convertToFile($file, FileUploader::ACTIVITY_MEDIA);

            $activity = \App\Services\ActivityImport\ActivityImporter::update($activity)
                ->addMedia([$uploadedFile])
                ->save();

            $this->addCaptions($uploadedFile);

            $this->succeeded('imported', [
                'file_id' => $uploadedFile->id,
                'activity_id' => $activity->id,
                'activity_name' => $activity->name
            ]);
        } catch (\Exception $e) {
            $this->failed('exception', ['message' => $e->getMessage(), 'filename' => (string) $file]);
            return;
        }
    }

    private function photoAlreadyImported(ZipFile $file): bool
    {
        return File::where('filename', $this->sanitiseFileName((string) $file))->exists();
    }

    public function type(): string
    {
        return 'photos';
    }

    private function getActivity(ZipFile $file): ?Activity
    {
        return Activity::whereAdditionalData('strava_photo_ids', $file->getFilenameWithoutExtAfter('photos/'))
            ->where('user_id', $this->user->id)
            ->first();
    }

    private function addCaptions(File $file)
    {
        $caption = $this->captions[$file->filename] ?? null;
        if ($caption) {
            $file->caption = $caption;
            $file->save();
        }
    }

}
