<?php

namespace App\Integrations\Strava\Import\Importers;

use Alchemy\Zippy\Archive\MemberInterface;
use App\Integrations\Strava\Tasks\SaveNewActivities;
use App\Models\Activity;
use App\Models\File;
use App\Services\File\FileUploader;
use App\Services\File\Upload;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ActivityImporter extends Importer
{

    protected function import()
    {
        $this->availableActivityFiles()->each(function(MemberInterface $member, int $key) {
            $this->updateProgress($key + 1, $this->availableActivityFiles()->count());
            $this->processActivityFile($member);
        });
    }

    private function availableActivityFiles(): Collection
    {
        return $this->zip->getMembers()
            ->filter(fn(MemberInterface $member) => Str::startsWith($member->getLocation(), 'activities/') && Str::length($member->getLocation()) > 11)
            ->values();
    }

    private function existingActivityUsingFile(MemberInterface $activityFile): ?Activity
    {
        $uploadId = Str::of(Str::substr($activityFile->getLocation(), 11))->before('.');
        return Activity::linkedTo('strava')->whereAdditionalDataContains('upload_id', $uploadId)->first();
    }

    public function type(): string
    {
        return 'activities';
    }

    private function processActivityFile(MemberInterface $activityFile)
    {
        try {
            $activity = $this->existingActivityUsingFile($activityFile);
            if($activity === null) {
                $this->failed('No activity could be found for the file ' . $activityFile->getLocation());
                return;
            }
            if($activity->activityFile()->exists()) {
                $this->failed('Activity ' . $activity->id . ' already has an activity file saved, skipping.');
                return;
            }

            $file = $this->convertMemberToFile($activityFile);

            $activity = \App\Services\ActivityImport\ActivityImporter::update($activity)
                ->withActivityFile($file)
                ->save();

            $this->succeeded('Activity file added to activity ' . $activity->id);
        } catch (\Exception $e) {
            $this->failed($e->getMessage());
            return;
        }
    }

    public function convertMemberToFile(MemberInterface $member): File
    {
        $fullMemberPath = $this->zip->getFullExtractedDirectory($member->getLocation());
        if($this->memberIsTarFile($member)) {
            $fullMemberPath = $this->unzipMember($member);
        }

        $filename = Str::substr($member->getLocation(), 11);
        if($this->memberIsTarFile($member)) {
            $filename = Str::replace('.gz', '', $filename);
        }

        return Upload::withContents(
            trim(file_get_contents($fullMemberPath)),
            $filename,
            $this->user,
            FileUploader::ARCHIVE
        );
    }

    private function memberIsTarFile(MemberInterface $member): bool
    {
        return Str::endsWith($member->getLocation(), '.gz');
    }

    private function unzipMember(MemberInterface $member): string
    {
        $path = $this->zip->getFullExtractedDirectory($member->getLocation());

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

}
