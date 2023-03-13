<?php

namespace App\Integrations\Strava\Import\Api\Resources;

use App\Services\Analysis\Parser\Point;
use JobStatus\JobStatusModifier;
use MStaack\LaravelPostgis\Geometries\LineString;

class Stats
{
    public function import(array $statsData, \App\Models\Activity $activity, JobStatusModifier $jobStatusModifier): Stats
    {
        if ($activity->started_at === null) {
            throw new \Exception('The activity must have a start date to retrieve stats from Strava');
        }

        if (!isset($statsData['time'])) {
            throw new \Exception('No time stream was returned from Strava');
        }

        $stats = $activity->statsFrom('strava')->firstOrFail();

        $jobStatusModifier->message('Using stats with ID of #' . $stats->id);

        $jobStatusModifier->message('Retrieved ' . count($statsData['time']['data']) . ' points from Strava');

        $jobStatusModifier->message('Generating point data');
        $pointData = $this->getPointData($statsData, $activity);
        $jobStatusModifier->message('Generated point data');

        $jobStatusModifier->message('Generating linestring data');
        $linestring = new LineString(collect($statsData['time']['data'])
            ->map(fn($timeDelta, $index) => new \MStaack\LaravelPostgis\Geometries\Point(
                data_get($statsData, 'latlng.data.' . $index . '.0', null),
                data_get($statsData, 'latlng.data.' . $index . '.1', null),
                data_get($statsData, 'altitude.data.' . $index, 0)
            ))
            ->all());
        $jobStatusModifier->message('Generated linestring data');

        $stats->fill(array_merge($pointData, [
            'linestring' => $linestring
        ]))->save();

        $jobStatusModifier->successMessage('Saved stats');

        return $this;
    }

    private function getPointData(array $statsData, \App\Models\Activity $activity): array
    {
        return collect($statsData['time']['data'])
            ->reduce(function ($data, $timeDelta, $index) use ($activity, $statsData) {
                $data['time_data'][] = $activity->started_at->addSeconds($timeDelta);
                $data['cadence_data'][] = $statsData['cadence']['data'][$index] ?? null;
                $data['temperature_data'][] = $statsData['temp']['data'][$index] ?? null;
                $data['heart_rate_data'][] = $statsData['heartrate']['data'][$index] ?? null;
                $data['speed_data'][] = $statsData['velocity_smooth']['data'][$index] ?? null;
                $data['cumulative_distance_data'][] = $statsData['distance']['data'][$index] ?? null;

                return $data;
            }, [
                'time_data' => [],
                'cadence_data' => [],
                'temperature_data' => [],
                'heart_rate_data' => [],
                'speed_data' => [],
                'cumulative_distance_data' => [],
            ]);

    }
}
