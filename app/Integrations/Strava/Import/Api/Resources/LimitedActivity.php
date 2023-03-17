<?php

namespace App\Integrations\Strava\Import\Api\Resources;

use App\Integrations\Strava\Jobs\LoadStravaActivity;
use App\Integrations\Strava\Jobs\LoadStravaComments;
use App\Integrations\Strava\Jobs\LoadStravaKudos;
use App\Integrations\Strava\Jobs\LoadStravaPhotos;
use App\Integrations\Strava\Jobs\LoadStravaStats;
use App\Models\Activity as ActivityModel;
use App\Models\Stats;
use App\Models\User;
use App\Services\ActivityImport\ActivityImporter;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Bus;
use MStaack\LaravelPostgis\Geometries\Point;

class LimitedActivity
{
    private ActivityModel $activity;

    public function getActivity(): ActivityModel
    {
        return $this->activity;
    }

    public function import(array $activityData, User $user): LimitedActivity
    {
        $existingActivity = $this->getExistingActivity($activityData);

        $this->activity = $existingActivity === null
            ? $this->createActivity($activityData, $user)
            : $this->updateActivity($activityData, $existingActivity);

        $this->throwChildJobs($this->activity, $activityData);

        return $this;
    }

    private function createActivity(array $activityData, User $user): ActivityModel
    {
        $importer = $this->populateImporter(ActivityImporter::for($user), $activityData);
        $importer->withName(\data_get($activityData, 'name'));

        $activity = $importer->import();

        $this->fillStats(
            $activityData,
            new Stats(['activity_id' => $activity->id])
        )->save();


        return $activity;
    }

    private function updateActivity(array $activityData, ActivityModel $existingActivity): ActivityModel
    {
        $importer = $this->populateImporter(ActivityImporter::update($existingActivity), $activityData);

        $stats = $this->fillStats(
            $activityData,
            $existingActivity->statsFrom('strava')->first() ?? new Stats(['activity_id' => $existingActivity->id])
        );
        $stats->save();

        return $importer->save();
    }

    public function getExistingActivity(array $activityData): ?ActivityModel
    {
        // Try and get the existing activity by its ID
        return array_key_exists('id', $activityData)
            ? ActivityModel::whereAdditionalData('strava_id', \data_get($activityData, 'id'))->first()
            : null;
    }

    private function getIntegerData(array $data, string $key)
    {
        return array_key_exists($key, $data) ? (int) $data[$key] : null;
    }

    private function populateImporter(ActivityImporter $importer, array $activityData)
    {
        return $importer
            ->linkTo('strava')
            ->setAdditionalData('strava_id', $this->getIntegerData($activityData, 'id'))
            ->setAdditionalData('strava_upload_id', $this->getIntegerData($activityData, 'upload_id_str'))
            ->setAdditionalData('strava_photo_count', $this->getIntegerData($activityData, 'total_photo_count'))
            ->setAdditionalData('strava_comment_count', $this->getIntegerData($activityData, 'comment_count'))
            ->setAdditionalData('strava_kudos_count', $this->getIntegerData($activityData, 'kudos_count'))
            ->setAdditionalData('strava_pr_count', $this->getIntegerData($activityData, 'pr_count'))
            ->setAdditionalData('strava_achievement_count', $this->getIntegerData($activityData, 'achievement_count'));
    }

    private function fillStats(array $activityData, Stats $stats): Stats
    {
        $stats->fill([
            'integration' => 'strava',
            'distance' => $activityData['distance'] ?? $stats->distance,
            'started_at' => isset($activityData['start_date']) ? Carbon::make($activityData['start_date']) : $stats->started_at,
            'duration' => $activityData['elapsed_time'] ?? $stats->duration,
            'average_speed' => $activityData['average_speed'] ?? $stats->average_speed,
            'min_altitude' => $activityData['elev_low'] ?? $stats->min_altitude,
            'max_altitude' => $activityData['elev_high'] ?? $stats->max_altitude,
            'elevation_gain' => $activityData['total_elevation_gain'] ?? $stats->elevation_gain,
            'moving_time' => $activityData['moving_time'] ?? $stats->moving_time,
            'max_speed' => $activityData['max_speed'] ?? $stats->max_speed,
            'average_cadence' => $activityData['average_cadence'] ?? $stats->average_cadence,
            'average_temp' => $activityData['average_temp'] ?? $stats->average_temp,
            'average_watts' => $activityData['average_watts'] ?? $stats->average_watts,
            'kilojoules' => $activityData['kilojoules'] ?? $stats->kilojoules,
            'start_point' => new Point(
                Arr::first($activityData['start_latlng'] ?? [$stats->start_point?->getLat()]),
                Arr::last($activityData['start_latlng'] ?? [$stats->start_point?->getLng()])
            ),
            'end_point' => new Point(
                Arr::first($activityData['end_latlng'] ?? [$stats->end_point?->getLat()]),
                Arr::last($activityData['end_latlng'] ?? [$stats->end_point?->getLng()])
            ),
        ]);

        return $stats;
    }

    private function throwChildJobs(ActivityModel $existingActivity, array $activityData)
    {
        $jobs = [
            new LoadStravaActivity($existingActivity),
            new LoadStravaStats($existingActivity),
        ];

        if ($this->getIntegerData($activityData, 'comment_count') > 0) {
            $jobs[] = new LoadStravaComments($existingActivity);
        }
        if ($this->getIntegerData($activityData, 'kudos_count') > 0) {
            $jobs[] = new LoadStravaKudos($existingActivity);
        }
        if ($this->getIntegerData($activityData, 'total_photo_count') > 0) {
            $jobs[] = new LoadStravaPhotos($existingActivity);
        }

        Bus::chain($jobs)->dispatch();

    }
}
