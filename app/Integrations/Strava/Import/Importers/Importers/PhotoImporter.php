<?php

namespace App\Integrations\Strava\Import\Importers\Importers;

use Alchemy\Zippy\Archive\MemberInterface;
use App\Integrations\Strava\Import\Importers\Importer;
use App\Models\Activity;
use App\Models\File;
use App\Services\File\FileUploader;
use App\Services\File\Upload;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class PhotoImporter extends Importer
{

    protected function import()
    {
        $captions = $this->zip->getCsv('photos.csv');

        $this->availablePhotos()->each(function(MemberInterface $member, int $key) use ($captions) {
            $this->updateProgress($key + 1, $this->availablePhotos()->count());
            $this->processFile($member, $captions);
        });
    }

    private function availablePhotos(): Collection
    {
        return $this->zip->getMembers()
            ->filter(fn(MemberInterface $member) => Str::startsWith($member->getLocation(), 'photos/') && Str::length($member->getLocation()) > 7)
            ->values();
    }

    private function photoExists(MemberInterface $file): ?File
    {
        $fileName = Str::substr($file->getLocation(), 7);
        return File::where('filename', $fileName)->first();
    }

    public function type(): string
    {
        return 'photos';
    }

    private function processFile(MemberInterface $member, array $captions)
    {
        try {
            $file = $this->photoExists($member);
            if($file !== null) {
                $this->failed('duplicate', [
                    'file_location' => $member->getLocation()
                ]);
                return;
            }

            $activity = Activity::whereAdditionalData('strava_photo_ids', (string) Str::of($member->getLocation())->after('photos/')->before('.'))
                ->where('user_id', $this->user->id)
                ->first();

            // Resolve the activity it is for and set $activity to the new activity
            $file = $this->convertMemberToFile($member);

            $caption = $captions[$member->getLocation()] ?? null;
            if($caption) {
                $file->caption = $caption;
                $file->save();
            }

            if($activity !== null) {
                $file->type = FileUploader::ACTIVITY_MEDIA;
                $file->save();

                $activity = \App\Services\ActivityImport\ActivityImporter::update($activity)
                    ->addMedia([$file])
                    ->save();
            }

            $this->succeeded('Photo imported', [
                'file_id' => $file->id,
                'matched_activity_id' => $activity?->id
            ]);
        } catch (\Exception $e) {
            $this->failed($e->getMessage(), ['reason' => 'exception']);
            return;
        }
    }

    public function convertMemberToFile(MemberInterface $member): File
    {
        $fullMemberPath = $this->zip->getFullExtractedDirectory($member->getLocation());

        $filename = Str::substr($member->getLocation(), 7);

        return Upload::withContents(
            trim(file_get_contents($fullMemberPath)),
            $filename,
            $this->user,
            FileUploader::UNMATCHED_MEDIA
        );
    }

}
