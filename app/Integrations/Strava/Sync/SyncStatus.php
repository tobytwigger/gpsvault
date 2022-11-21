<?php

namespace App\Integrations\Strava\Sync;

use App\Models\Activity;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use JobStatus\Models\JobStatus;
use JobStatus\Models\JobStatusTag;

class SyncStatus
{
    private User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public static function forUser(User $user)
    {
        return new static($user);
    }

    public function toArray()
    {
        return [
            'kudos' => $this->kudosSyncingJobs(),
            'comments' => $this->commentsSyncingJobs(),
            'photos' => $this->photosSyncingJobs(),
            'stats' => $this->statsSyncingJobs(),
            'activities' => $this->activitySyncingJobs(),
            'activities_linked' => $this->user->activities()->whereHasAdditionalData('strava_id')->count(),
            'unlinked_activities' => $this->user->activities()
                ->whereDoesntHave('additionalData', fn (Builder $subquery) => $subquery->where('key', 'strava_id'))
                ->get(),
            'total_activities' => $this->user->activities()->count(),
            'is_indexing' => $this->isIndexing()
        ];
    }

    public function kudosSyncingJobs(): Collection
    {
        return $this->getActivitiesFrom(JobStatus::forJobAlias('load-strava-kudos')
            ->whereNotStatus(['cancelled', 'failed', 'succeeded'])
            ->whereTag('user_id', $this->user->id)
            ->get());
    }

    public function commentsSyncingJobs(): Collection
    {
        return $this->getActivitiesFrom(JobStatus::forJobAlias('load-strava-comments')
            ->whereNotStatus(['cancelled', 'failed', 'succeeded'])
            ->whereTag('user_id', $this->user->id)
            ->get());
    }

    public function photosSyncingJobs(): Collection
    {
        return $this->getActivitiesFrom(JobStatus::forJobAlias('load-strava-photos')
            ->whereNotStatus(['cancelled', 'failed', 'succeeded'])
            ->whereTag('user_id', $this->user->id)
            ->get());
    }

    public function statsSyncingJobs(): Collection
    {
        return $this->getActivitiesFrom(JobStatus::forJobAlias('load-strava-stats')
            ->whereNotStatus(['cancelled', 'failed', 'succeeded'])
            ->whereTag('user_id', $this->user->id)
            ->get());
    }

    public function activitySyncingJobs(): Collection
    {
        return $this->getActivitiesFrom(JobStatus::forJobAlias('load-strava-activity')
            ->whereNotStatus(['cancelled', 'failed', 'succeeded'])
            ->whereTag('user_id', $this->user->id)
            ->get());
    }

    private function getActivitiesFrom(Collection $jobStatuses): Collection
    {
        $jobStatusIds = $jobStatuses->map(fn (JobStatus $jobStatus) => $jobStatus->id);
        $tags = JobStatusTag::whereIn('job_status_id', $jobStatusIds)->where('key', 'activity_id')->get();
        $activityIds = $tags->map(fn (JobStatusTag $tag) => $tag->value);

        return $this->user->activities()->whereIn('id', $activityIds)->select('id', 'name')->get()->map(fn (Activity $activity) => [
            'id' => $activity->id, 'name' => $activity->name,
        ]);
    }

    public function isIndexing(): bool
    {
        return JobStatus::forJobAlias('sync-activities')
            ->whereNotStatus(['canceled', 'failed', 'succeeded'])
            ->whereTag('user_id', $this->user->id)
            ->exists();
    }
}
