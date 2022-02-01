<?php

namespace App\Integrations\Strava\Client\Import\Resources;

use App\Exceptions\ActivityDuplicate;
use App\Integrations\Strava\Events\StravaActivityCommentsUpdated;
use App\Integrations\Strava\Events\StravaActivityKudosUpdated;
use App\Integrations\Strava\Events\StravaActivityPhotosUpdated;
use App\Integrations\Strava\Events\StravaActivityUpdated;
use App\Models\Stats;
use App\Models\User;
use App\Services\ActivityImport\ActivityImporter;
use Carbon\Carbon;
use Illuminate\Support\Arr;

class Activity
{

    public const CREATED = 'created';

    public const UPDATED = 'updated';

    private ?string $status = null;

    private User $user;

    private \App\Models\Activity $activity;

    public function forUser(User $user): static
    {
        $this->user = $user;
        return $this;
    }

    public function getActivity(): \App\Models\Activity
    {
        return $this->activity;
    }

    public function import(array $activityData, User $user): Activity
    {
        $existingActivity = $this->getExistingActivity($activityData);

        $this->activity = $existingActivity === null
            ? $this->createActivity($activityData, $user)
            : $this->updateActivity($activityData, $existingActivity);

        return $this;
    }

    public function getExistingActivity(array $activityData): ?\App\Models\Activity
    {
        // Try and get the existing activity by its ID
        return array_key_exists('id', $activityData)
            ? Activity::whereAdditionalData('strava_id', data_get($activityData, 'id'))->first()
            : null;
    }

    //    private function getIntegerData(string $key)
//    {
//        return array_key_exists($key, $activityData) ? (int) $activityData[$key] : null;
//    }

    private function createActivity(array $activityData, User $user): \App\Models\Activity
    {
        $this->status = static::CREATED;

        $activity = ActivityImporter::for($user)
            ->withName(data_get($activityData, 'name'))
            ->linkTo('strava')
            ->setAdditionalData('strava_id', $this->getIntegerData('id'))
            ->setAdditionalData('strava_upload_id', $this->getIntegerData('upload_id_str'))
            ->setAdditionalData('strava_photo_count', $this->getIntegerData('total_photo_count'))
            ->setAdditionalData('strava_comment_count', $this->getIntegerData('comment_count'))
            ->setAdditionalData('strava_kudos_count', $this->getIntegerData('kudos_count'))
            ->setAdditionalData('strava_pr_count', $this->getIntegerData('pr_count'))
            ->setAdditionalData('strava_achievement_count', $this->getIntegerData('achievement_count'))
            ->import();

        $stats = $this->fillStats(new Stats(['stats_id' => $activity->id, 'stats_type' => get_class($activity)]))->save();

        StravaActivityUpdated::dispatch($activity);

        if ($this->getIntegerData('comment_count') > 0) {
            StravaActivityCommentsUpdated::dispatch($activity);
        }
        if ($this->getIntegerData('kudos_count') > 0) {
            StravaActivityKudosUpdated::dispatch($activity);
        }
        if ($this->getIntegerData('total_photo_count') > 0) {
            StravaActivityPhotosUpdated::dispatch($activity);
        }

        return $activity;
    }

    private function updateActivity(array $activityData, Activity $existingActivity): \App\Models\Activity
    {
        $importer = ActivityImporter::update($existingActivity);
        $updated = [];

        if ($existingActivity->getAdditionalData('strava_photo_count') !== $this->getIntegerData('total_photo_count')) {
            $importer->setAdditionalData('strava_photo_count', $this->getIntegerData('total_photo_count'));
            $updated[] = $this->getIntegerData('total_photo_count') > 0 ? 'photos' : null;
        }

        if ($existingActivity->getAdditionalData('strava_comment_count') !== $this->getIntegerData('comment_count')) {
            $importer->setAdditionalData('strava_comment_count', $this->getIntegerData('comment_count'));
            $updated[] = $this->getIntegerData('comment_count') > 0 ? 'comments' : null;
        }

        if ($existingActivity->getAdditionalData('strava_kudos_count') !== $this->getIntegerData('kudos_count')) {
            $importer->setAdditionalData('strava_kudos_count', $this->getIntegerData('kudos_count'));
            $updated[] = $this->getIntegerData('kudos_count') > 0 ? 'kudos' : null;
        }


        if ($existingActivity->getAdditionalData('strava_pr_count') !== $this->getIntegerData('pr_count')
            || $existingActivity->getAdditionalData('strava_achievement_count') !== $this->getIntegerData('achievement_count')) {
            $importer
                ->setAdditionalData('strava_pr_count', $this->getIntegerData('kudos_count'))
                ->setAdditionalData('strava_achievement_count', $this->getIntegerData('achievement_count'));
            $updated[] = $this->getIntegerData('kudos_count') > 0 ? 'details' : null;
        }

        $this->fillStats($existingActivity->statsFrom('strava')->first() ?? new Stats(['stats_id' => $existingActivity->id, 'stats_type' => get_class($existingActivity)]))
            ->save();

        $existingActivity = $importer->save();

        $events = [
            'photos' => StravaActivityPhotosUpdated::class,
            'comments' => StravaActivityCommentsUpdated::class,
            'kudos' => StravaActivityKudosUpdated::class,
            'details' => StravaActivityUpdated::class
        ];
        foreach(array_filter($updated) as $updatedProperty) {
            if(array_key_exists($updatedProperty, $events)) {
                $events[$updatedProperty]::dispatch($existingActivity);
            }
        }
        if (count(array_filter($updated)) > 0) {
            $this->status = static::UPDATED;
        }

        return $existingActivity;
    }

    private function fillStats(Stats $stats): Stats
    {
        $stats->fill([
            'integration' => 'strava',
            'distance' => $activityData['distance'] ?? null,
            'started_at' => isset($activityData['start_date']) ? Carbon::make($activityData['start_date']) : null,
            'duration' => $activityData['elapsed_time'] ?? null,
            'average_speed' => $activityData['average_speed'] ?? null,
            'min_altitude' => $activityData['elev_low'] ?? null,
            'max_altitude' => $activityData['elev_high'] ?? null,
            'elevation_gain' => $activityData['total_elevation_gain'] ?? null,
            'moving_time' => $activityData['moving_time'] ?? null,
            'max_speed' => $activityData['max_speed'] ?? null,
            'average_cadence' => $activityData['average_cadence'] ?? null,
            'average_temp' => $activityData['average_temp'] ?? null,
            'average_watts' => $activityData['average_watts'] ?? null,
            'kilojoules' => $activityData['kilojoules'] ?? null,
            'start_latitude' => Arr::first($activityData['start_latlng'] ?? []),
            'start_longitude' => Arr::last($activityData['start_latlng'] ?? []),
            'end_latitude' => Arr::first($activityData['end_latlng'] ?? []),
            'end_longitude' => Arr::last($activityData['end_latlng'] ?? []),
        ]);
        return $stats;
    }

    public function status(): string
    {
        return $this->status;
    }

}
