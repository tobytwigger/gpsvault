<?php

namespace App\Integrations\Strava;

use App\Integrations\Strava\Import\Models\Import;
use App\Models\Activity;
use App\Models\File;
use App\Models\User;
use App\Integrations\Integration;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class StravaIntegration extends Integration
{

    public function id(): string
    {
        return 'strava';
    }

    public function serviceUrl(): string
    {
        return 'https://www.strava.com/';
    }

    public function name(): string
    {
        return 'Strava';
    }

    public function description(): string
    {
        return 'Strava is xyz';
    }

    public function functionality(): array
    {
        return [
            'Import all your activities and their interactions.',
            'Import all photos uploaded to Strava.'
        ];
    }

    public function connected(User $user): bool
    {
        return $user->stravaTokens()->exists();
    }

    public function loginUrl(): string
    {
        return route('strava.login');
    }

    public function disconnect(User $user): void
    {
        $user->stravaTokens()->withoutGlobalScope('enabled')->withoutGlobalScope('not-expired')->delete();
    }

    public function vueAddOn(): ?string
    {
        return 'strava-integration-addon';
    }

    public function vueAddOnProps(): array
    {
        return [
            'activitiesLoading' => 1 ?? Activity::whereAdditionalData('strava_is_loading_photos', true)
                ->orWhere(fn(Builder $query) => $query->whereAdditionalData('strava_is_loading_details', true))
                    ->count(),
//            'neededActivities' => Activity::linkedTo('strava')
//                ->whereDoesntHave('activityFile')
//                ->when($lastRun, fn(Builder $query) => $query->where('created_at', '>', $lastRun->))
            'activitiesWithoutFile' => 1 ?? Activity::linkedTo('strava')->whereDoesntHave('activityFile')->count(),
            'activitiesWithoutPhotos' => 1 ?? Activity::linkedTo('strava')
                ->whereHasAdditionalData('strava_photo_ids')
                ->withCount('files')
                ->with('files')
                ->with('additionalActivityData')
                ->get()
                ->filter(function (Activity $activity) {
                    $uploadedFiles = $activity->files->map(fn(File $file) => Str::of($file->filename)->before('.'));
                    return collect(Arr::wrap($activity->getAdditionalData('strava_photo_ids')))
                        ->filter(fn(string $photoId) => $uploadedFiles->contains($photoId))
                        ->count() > 0;
                 })
                ->count()
        ];
    }

}
