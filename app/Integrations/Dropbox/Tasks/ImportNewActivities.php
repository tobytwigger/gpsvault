<?php

namespace App\Integrations\Dropbox\Tasks;

use App\Integrations\Dropbox\Client\Dropbox;
use App\Integrations\Dropbox\Models\DropboxToken;
use App\Models\Activity;
use App\Models\File;
use App\Models\User;
use App\Services\ActivityImport\ActivityImporter;
use App\Services\File\FileUploader;
use App\Services\File\Upload;
use App\Services\Sync\Task;
use Kunnu\Dropbox\Models\FileMetadata;

class ImportNewActivities extends Task
{

    public function description(): string
    {
        return 'Import any new activities from a specific folder in Dropbox.';
    }

    public function name(): string
    {
        return 'Import from Dropbox';
    }

    public function disableBecause(User $user): ?string
    {
        if (!DropboxToken::where('user_id', $user->id)->exists()) {
            return 'Your account must be connected to Dropbox';
        }
        return null;
    }

    public function run()
    {
        $this->line('Checking dropbox');

        $fileMetadatas = collect();
        $files = Dropbox::client($this->user())->listFolder('/Apps/WahooFitness');

        $fileMetadatas = $fileMetadatas->merge(
            $files->getItems()
                ->filter(fn(FileMetadata $fileMetadata) => File::where('type', FileUploader::ACTIVITY_FILE)->where('filename', $fileMetadata->getName())->count() === 0)
            ->values()
        );

        if($files->hasMoreItems()) {
            do {
                $files = Dropbox::client($this->user())->listFolderContinue($files->getCursor());
                $fileMetadatas = $fileMetadatas->merge(
                    $files->getItems()
                        ->filter(fn(FileMetadata $fileMetadata) => File::where('type', FileUploader::ACTIVITY_FILE)->where('filename', $fileMetadata->getName())->count() === 0)
                        ->values()
                );
            } while ($files->hasMoreItems());
        }

        $this->line(sprintf('Found %u new files.', $fileMetadatas->count()));



        $activities = collect();

        /** @var FileMetadata $fileMetadata */
        foreach($fileMetadatas as $fileMetadata) {
            $file = Upload::withContents(
                Dropbox::client($this->user())
                    ->download($fileMetadata->getPathLower())->getContents(), $fileMetadata->getName(), $this->user(), FileUploader::ACTIVITY_FILE
            );
            $activities->push(
                ActivityImporter::for($this->user())->withActivityFile($file)->import()
            );
        }

        $this->succeed(sprintf('Imported %u activities.', $activities->count()));

    }
}
