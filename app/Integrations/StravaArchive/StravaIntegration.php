<?php

namespace App\Integrations\Strava;

use App\Models\Activity;
use App\Models\File;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class StravaIntegration
{
    public function vueAddOnProps(): array
    {
        $timeUntilReady = null;
        foreach (StravaServiceProvider::stravaLimiters() as $limit) {
            $key = md5('strava' . $limit->key);
            if (RateLimiter::tooManyAttempts($key, $limit->maxAttempts)) {
                $newTimeUntilReady = RateLimiter::availableIn($key);
                $timeUntilReady = $newTimeUntilReady === null && ($timeUntilReady === null || $timeUntilReady > $newTimeUntilReady)
                    ? $timeUntilReady
                    : $newTimeUntilReady;
            }
        }

        return [
            'activitiesLoadingKudos' => Activity::whereAdditionalData('strava_is_loading_kudos', true)->count(),
            'activitiesLoadingComments' => Activity::whereAdditionalData('strava_is_loading_comments', true)->count(),
            'activitiesLoadingStats' => Activity::whereAdditionalData('strava_is_loading_stats', true)->count(),
            'activitiesLoadingPhotos' => Activity::whereAdditionalData('strava_is_loading_photos', true)->count(),
            'activitiesLoadingBasicData' => Activity::whereAdditionalData('strava_is_loading_details', true)->count(),
            'activitiesWithoutFiles' => Activity::linkedTo('strava')->whereDoesntHave('file')->count(),
            'activitiesWithoutPhotos' => Activity::linkedTo('strava')
                ->whereHasAdditionalData('strava_photo_ids')
                ->withCount('files')
                ->with('files')
                ->with('additionalData')
                ->get()
                ->filter(function (Activity $activity) {
                    $uploadedFiles = $activity->files->map(fn (File $file) => Str::of($file->filename)->before('.'));

                    return collect($activity->getAdditionalData('strava_photo_ids'))
                        ->filter(fn (string $photoId) => !$uploadedFiles->contains($photoId))
                        ->count() > 0;
                })
                ->count(),
            'timeUntilReady' => $timeUntilReady
        ];
    }
}
