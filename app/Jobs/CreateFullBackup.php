<?php

namespace App\Jobs;

use App\Models\Activity;
use App\Models\Route;
use App\Models\Tour;
use App\Models\User;
use App\Services\Archive\ZipCreator;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use JobStatus\Trackable;

class CreateFullBackup implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Trackable;

    public User $user;

    /**
     * Create a new job instance.
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function alias(): ?string
    {
        return 'create-full-backup';
    }

    public function tags(): array
    {
        return [
            'user_id' => $this->user->id
        ];
    }

    public static function canSeeTracking($user = null, array $tags = []): bool
    {
        return (int) $tags['user_id'] === $user?->id;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $this->line('Collecting data to back up.');
        $zipCreator = ZipCreator::start($this->user);

        /* User */
        $zipCreator->add($this->user);

        /* Activity */
        $activityCount = 0;
        foreach (Activity::where('user_id', $this->user->id)->get() as $activity) {
            $zipCreator->add($activity);
            $activityCount++;
        }
        $this->line(sprintf('Added %u activities.', $activityCount));

        /* Route */
        $routeCount = 0;
        foreach (Route::where('user_id', $this->user->id)->get() as $route) {
            $zipCreator->add($route);
            $routeCount++;
        }
        $this->line(sprintf('Added %u routes.', $routeCount));

        /* Tour */
        $tourCount = 0;
        foreach (Tour::where('user_id', $this->user->id)->get() as $tour) {
            $zipCreator->add($tour);
            $tourCount++;
        }
        $this->line(sprintf('Added %u tours.', $tourCount));

        $this->checkForSignals();

        $this->line('Generating archive.');

        $file = $zipCreator->archive();
        $file->title = $file->title ?? 'Full backup ' . Carbon::now()->format('d/m/Y');
        $file->caption = $file->caption ?? 'Full backup taken at ' . Carbon::now()->format('d/m/Y H:i:s');
        $file->save();

        $this->successMessage('Generated full backup of your data.');
    }

    public function onCancel()
    {
        $this->warningMessage('Cancelled without generating an archive.');
    }
}
