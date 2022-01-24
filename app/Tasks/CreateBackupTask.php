<?php

namespace App\Tasks;

use App\Integrations\Strava\Client\Import\ImportStravaActivity;
use App\Integrations\Strava\Client\Strava;
use App\Models\Activity;
use App\Services\Sync\Sync;
use App\Models\User;
use App\Services\Archive\ZipCreator;
use App\Services\Sync\Task;
use Carbon\Carbon;

class CreateBackupTask extends Task
{

    public static function id(): string
    {
        return 'backup-all-tasks';
    }

    public function run()
    {
        $this->line('Collecting data to back up.');
        $zipCreator = ZipCreator::start($this->user());
        $zipCreator->add($this->user());

        $activityCount = 0;
        foreach(Activity::where('user_id', $this->user()->id)->get() as $activity) {
            $zipCreator->add($activity);
            $activityCount++;
        }
        $this->line(sprintf('Added %u activities.', $activityCount));


        $syncCount = 0;
        foreach(Sync::where('user_id', $this->user()->id)->get() as $sync) {
            $zipCreator->add($sync);
            $syncCount++;
        }
        $this->line(sprintf('Added %u syncs.', $syncCount));

        $this->offerBail('Cancelled without generating an archive.');

        $this->line('Generating archive.');

        $file = $zipCreator->archive();
        $file->title = $file->title ?? 'Full backup ' . Carbon::now()->format('d/m/Y');
        $file->caption = $file->title ?? 'Full backup taken at ' . Carbon::now()->format('dd/mm/yyyy H:i:s');
        $file->save();

        $this->succeed('Generated full backup of your data.');
    }



}
