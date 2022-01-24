<?php

namespace App\Integrations\Dropbox\Tasks;

use App\Integrations\Dropbox\Client\Dropbox;
use App\Integrations\Dropbox\Models\DropboxToken;
use App\Models\Activity;
use App\Services\Sync\Sync;
use App\Models\User;
use App\Services\Archive\ZipCreator;
use App\Services\Sync\Task;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Kunnu\Dropbox\DropboxFile;

class ExportFullBackup extends Task
{

    public function description(): string
    {
        return 'Export a full backup of all your data to Dropbox';
    }

    public function name(): string
    {
        return 'Export to Dropbox';
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
        $this->line('Creating archive');
        $zipCreator = ZipCreator::start($this->user());
        $zipCreator->add($this->user());
        foreach(Activity::where('user_id', $this->user()->id)->get() as $activity) {
            $zipCreator->add($activity);
        }
        foreach(Sync::where('user_id', $this->user()->id)->get() as $sync) {
            $zipCreator->add($sync);
        }
        $file = $zipCreator->archive();

        $this->line('Uploading to Dropbox');

        $fileName = 'Full backup - ' . Carbon::now()->format('d M Y H:i:s') . '.zip';
        Storage::disk('temp')->put('/dropbox/backup-task/' . $fileName, $file->getFileContents());
        Dropbox::client($this->user())->upload(
            DropboxFile::createByPath(Storage::disk('temp')->path('/dropbox/backup-task/' . $fileName), DropboxFile::MODE_WRITE),
            '/Apps/CycleStore/' . $fileName
        );

        $this->succeed('Uploaded to Dropbox');
    }
}
