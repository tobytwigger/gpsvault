<?php

namespace App\Integrations\Strava\Client\Import;

use App\Exceptions\ActivityDuplicate;
use App\Integrations\Strava\Events\StravaActivityCommentsUpdated;
use App\Integrations\Strava\Events\StravaActivityKudosUpdated;
use App\Integrations\Strava\Events\StravaActivityPhotosUpdated;
use App\Integrations\Strava\Events\StravaActivityUpdated;
use App\Models\Activity;
use App\Models\ActivityStats;
use App\Models\User;
use App\Services\ActivityImport\ActivityImporter;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;

class ImportStravaActivity
{

    private array $data;

    public const CREATED = 'created';

    public const UPDATED = 'updated';

    public const LINKED = 'linked';

    private ?string $action = null;

    private User $user;

    private ?Activity $activity = null;

    public function __construct(array $data, User $user)
    {
        $this->data = $data;
        $this->user = $user;
    }

    /**
     * @throws ActivityDuplicate
     */
    public static function importFromApi(array $data, User $user): static
    {
        return (new static($data, $user))->import();
    }

    public function import(): ImportStravaActivity
    {
        try {
            $existingActivity = $this->getExistingActivity();
        } catch (ActivityDuplicate $e) {
            $this->linkActivity($e->activity);
            return $this;
        }

        $this->activity = $existingActivity === null
            ? $this->createActivity()
            : $this->updateActivity($existingActivity);

        return $this;
    }

    /**
     * @throws ActivityDuplicate
     */
    public function getExistingActivity(): ?Activity
    {
        $existing = array_key_exists('id', $this->data)
            ? Activity::whereAdditionalData('strava_id', $this->data['id'])->first()
            : null;
        if($existing === null && ($this->data['start_date'] ?? false) && ($this->data['distance'] ?? false)) {
            $existing = Activity::where('user_id', $this->user->id)
                ->whereBetween('started_at', [Carbon::make($this->data['start_date'])->subSeconds(30), Carbon::make($this->data['start_date'])->addSeconds(30)])
                ->where('distance', '<', (float) $this->data['distance'] + 2.0)
                ->where('distance', '>', (float) $this->data['distance'] - 2.0)
                ->first();
            if($existing !== null) {
                $existing->linked_to = array_unique(array_merge($existing->linked_to, ['strava']));
                $existing->save();
                $existing->setAdditionalData('strava_id', $this->data['id']);
                throw new ActivityDuplicate($existing);
            }
        }
        return $existing;
    }

    public function wasUpdated(): bool
    {
        return $this->action === static::UPDATED;
    }

    public function wasCreated(): bool
    {
        return $this->action === static::CREATED;
    }

    public function wasLinked(): bool
    {
        return $this->action === static::LINKED;
    }

    private function getIntegerData(string $key)
    {
        return array_key_exists($key, $this->data) ? (int) $this->data[$key] : null;
    }

    private function createActivity(): Activity
    {
        $this->action = static::CREATED;

        $activity = ActivityImporter::for($this->user)
            ->withName(data_get($this->data, 'name'))
            ->linkTo('strava')
            ->setAdditionalData('strava_id', $this->getIntegerData('id'))
            ->setAdditionalData('strava_upload_id', $this->getIntegerData('upload_id_str'))
            ->setAdditionalData('strava_photo_count', $this->getIntegerData('total_photo_count'))
            ->setAdditionalData('strava_comment_count', $this->getIntegerData('comment_count'))
            ->setAdditionalData('strava_kudos_count', $this->getIntegerData('kudos_count'))
            ->setAdditionalData('strava_pr_count', $this->getIntegerData('pr_count'))
            ->setAdditionalData('strava_achievement_count', $this->getIntegerData('achievement_count'))
            ->import();

        $stats = $this->fillStats(new ActivityStats(['activity_id' => $activity->id]))->save();

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

    private function updateActivity(Activity $existingActivity): Activity
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

        $this->fillStats($existingActivity->activityStatsFrom('strava')->first() ?? new ActivityStats(['activity_id' => $existingActivity->id]))
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
            $this->action = static::UPDATED;
        }

        return $existingActivity;
    }

    private function linkActivity(Activity $activity)
    {
        $this->action = static::CREATED;

        $importer = ActivityImporter::update($activity);
        if($activity->name === 'New Ride') {
            $importer->withName(data_get($this->data, 'name'));
        }
        $activity = $importer
            ->linkTo('strava')
            ->setAdditionalData('strava_id', $this->getIntegerData('id'))
            ->setAdditionalData('strava_upload_id', $this->getIntegerData('upload_id_str'))
            ->setAdditionalData('strava_photo_count', $this->getIntegerData('total_photo_count'))
            ->setAdditionalData('strava_comment_count', $this->getIntegerData('comment_count'))
            ->setAdditionalData('strava_kudos_count', $this->getIntegerData('kudos_count'))
            ->setAdditionalData('strava_pr_count', $this->getIntegerData('pr_count'))
            ->setAdditionalData('strava_achievement_count', $this->getIntegerData('achievement_count'))
            ->import();

        $stats = $this->fillStats($activity->activityStatsFrom('strava')->first() ?? new ActivityStats(['activity_id' => $activity->id]))->save();

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

    private function fillStats(ActivityStats $stats): ActivityStats
    {
        $stats->fill([
            'integration' => 'strava',
            'distance' => $this->data['distance'] ?? null,
            'started_at' => isset($this->data['start_date']) ? Carbon::make($this->data['start_date']) : null,
            'duration' => $this->data['elapsed_time'] ?? null,
            'average_speed' => $this->data['average_speed'] ?? null,
            'min_altitude' => $this->data['elev_low'] ?? null,
            'max_altitude' => $this->data['elev_high'] ?? null,
            'elevation_gain' => $this->data['total_elevation_gain'] ?? null,
            'moving_time' => $this->data['moving_time'] ?? null,
            'max_speed' => $this->data['max_speed'] ?? null,
            'average_cadence' => $this->data['average_cadence'] ?? null,
            'average_temp' => $this->data['average_temp'] ?? null,
            'average_watts' => $this->data['average_watts'] ?? null,
            'kilojoules' => $this->data['kilojoules'] ?? null,
            'start_latitude' => Arr::first($this->data['start_latlng'] ?? []),
            'start_longitude' => Arr::last($this->data['start_latlng'] ?? []),
            'end_latitude' => Arr::first($this->data['end_latlng'] ?? []),
            'end_longitude' => Arr::last($this->data['end_latlng'] ?? []),
        ]);
        return $stats;
    }

}
