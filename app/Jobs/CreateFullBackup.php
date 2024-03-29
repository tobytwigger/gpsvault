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
use JobStatus\Concerns\Trackable;

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
            'user_id' => $this->user->id,
        ];
    }

    public function users(): array
    {
        return [$this->user->id];
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
        $this->status()->line('Collecting data to back up.');
        $zipCreator = ZipCreator::start($this->user);

        /* User */
        $zipCreator->add($this->user);
        $this->status()->setPercentage(5);
        $this->checkForSignals();

        /* Activity */
        $activityCount = 0;
        foreach (Activity::where('user_id', $this->user->id)->get() as $activity) {
            $zipCreator->add($activity);
            $activityCount++;
        }
        $this->status()->line(sprintf('Added %u activities.', $activityCount));
        $this->status()->setPercentage(10);
        $this->checkForSignals();

        /* Route */
        $routeCount = 0;
        foreach (Route::where('user_id', $this->user->id)->get() as $route) {
            $zipCreator->add($route);
            $routeCount++;
        }
        $this->status()->line(sprintf('Added %u routes.', $routeCount));
        $this->status()->setPercentage(20);
        $this->checkForSignals();

        /* Tour */
        $tourCount = 0;
        foreach (Tour::where('user_id', $this->user->id)->get() as $tour) {
            $zipCreator->add($tour);
            $tourCount++;
        }
        $this->status()->line(sprintf('Added %u tours.', $tourCount));
        $this->status()->setPercentage(30);

        $this->checkForSignals();

        $this->status()->line('Generating archive.');

        $file = $zipCreator->archive();
        $this->status()->setPercentage(95);
        $file->title = $file->title ?? 'Full backup ' . Carbon::now()->format('d/m/Y');
        $file->caption = $file->caption ?? 'Full backup taken at ' . Carbon::now()->format('d/m/Y H:i:s');
        $file->save();

        $this->status()->successMessage('Generated full backup of your data.');
    }

    public function onCancel()
    {
        $this->status()->warningMessage('Cancelled without generating an archive.');
    }
}
