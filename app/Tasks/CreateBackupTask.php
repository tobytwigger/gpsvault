<?php

namespace App\Tasks;

use App\Models\Activity;
use App\Models\Route;
use App\Models\Tour;
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
        foreach (Activity::where('user_id', $this->user()->id)->get() as $activity) {
            $zipCreator->add($activity);
            $activityCount++;
        }
        $this->line(sprintf('Added %u activities.', $activityCount));

        $routeCount = 0;
        foreach (Route::where('user_id', $this->user()->id)->get() as $route) {
            $zipCreator->add($route);
            $routeCount++;
        }
        $this->line(sprintf('Added %u routes.', $routeCount));

        $tourCount = 0;
        foreach (Tour::where('user_id', $this->user()->id)->get() as $tour) {
            $zipCreator->add($tour);
            $tourCount++;
        }
        $this->line(sprintf('Added %u tours.', $tourCount));

        $this->offerBail('Cancelled without generating an archive.');

        $this->line('Generating archive.');

        $file = $zipCreator->archive();
        $file->title = $file->title ?? 'Full backup ' . Carbon::now()->format('d/m/Y');
        $file->caption = $file->caption ?? 'Full backup taken at ' . Carbon::now()->format('d/m/Y H:i:s');
        $file->save();

        $this->succeed('Generated full backup of your data.');
    }
}
