<?php

namespace App\Models;

use App\Jobs\GenerateActivityThumbnail;
use App\Services\Geocoding\Geocoder;
use App\Services\PolylineEncoder\GooglePolylineEncoder;
use App\Settings\StatsOrder;
use App\Traits\HasAdditionalData;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use MStaack\LaravelPostgis\Eloquent\PostgisTrait;
use MStaack\LaravelPostgis\Geometries\LineString;
use MStaack\LaravelPostgis\Geometries\Point;

/**
 * @property int $id
 * @property string $integration
 * @property float|null $distance
 * @property \Illuminate\Support\Carbon|null $started_at
 * @property \Illuminate\Support\Carbon|null $finished_at
 * @property float|null $duration
 * @property float|null $average_speed
 * @property float|null $average_pace
 * @property float|null $min_altitude
 * @property float|null $max_altitude
 * @property float|null $elevation_gain
 * @property float|null $elevation_loss
 * @property float|null $moving_time
 * @property float|null $max_speed
 * @property float|null $average_cadence
 * @property float|null $average_temp
 * @property float|null $average_watts
 * @property float|null $kilojoules
 * @property mixed|null $start_point
 * @property mixed|null $end_point
 * @property float|null $max_heartrate
 * @property float|null $average_heartrate
 * @property float|null $calories
 * @property LineString|null $linestring
 * @property int $activity_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read int|null $activity_points_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\AdditionalData[] $additionalData
 * @property-read int|null $additional_data_count
 * @property-read mixed $additional_data
 * @property-read mixed $human_ended_at
 * @property-read mixed $human_started_at
 * @property-read mixed $linestring_with_distance
 * @property-read Model|\Eloquent $model
 * @method static \Database\Factories\StatsFactory factory(...$parameters)
 * @method static \MStaack\LaravelPostgis\Eloquent\Builder|Stats newModelQuery()
 * @method static \MStaack\LaravelPostgis\Eloquent\Builder|Stats newQuery()
 * @method static \MStaack\LaravelPostgis\Eloquent\Builder|Stats orderByPreference()
 * @method static \MStaack\LaravelPostgis\Eloquent\Builder|Stats preferred()
 * @method static \MStaack\LaravelPostgis\Eloquent\Builder|Stats query()
 * @method static \MStaack\LaravelPostgis\Eloquent\Builder|Stats whereAdditionalData(string $key, $value)
 * @method static \MStaack\LaravelPostgis\Eloquent\Builder|Stats whereAverageCadence($value)
 * @method static \MStaack\LaravelPostgis\Eloquent\Builder|Stats whereAverageHeartrate($value)
 * @method static \MStaack\LaravelPostgis\Eloquent\Builder|Stats whereAveragePace($value)
 * @method static \MStaack\LaravelPostgis\Eloquent\Builder|Stats whereAverageSpeed($value)
 * @method static \MStaack\LaravelPostgis\Eloquent\Builder|Stats whereAverageTemp($value)
 * @method static \MStaack\LaravelPostgis\Eloquent\Builder|Stats whereAverageWatts($value)
 * @method static \MStaack\LaravelPostgis\Eloquent\Builder|Stats whereCalories($value)
 * @method static \MStaack\LaravelPostgis\Eloquent\Builder|Stats whereCreatedAt($value)
 * @method static \MStaack\LaravelPostgis\Eloquent\Builder|Stats whereDistance($value)
 * @method static \MStaack\LaravelPostgis\Eloquent\Builder|Stats whereDuration($value)
 * @method static \MStaack\LaravelPostgis\Eloquent\Builder|Stats whereElevationGain($value)
 * @method static \MStaack\LaravelPostgis\Eloquent\Builder|Stats whereElevationLoss($value)
 * @method static \MStaack\LaravelPostgis\Eloquent\Builder|Stats whereEndPoint($value)
 * @method static \MStaack\LaravelPostgis\Eloquent\Builder|Stats whereFinishedAt($value)
 * @method static \MStaack\LaravelPostgis\Eloquent\Builder|Stats whereHasAdditionalData(string $key)
 * @method static \MStaack\LaravelPostgis\Eloquent\Builder|Stats whereId($value)
 * @method static \MStaack\LaravelPostgis\Eloquent\Builder|Stats whereIntegration($value)
 * @method static \MStaack\LaravelPostgis\Eloquent\Builder|Stats whereKilojoules($value)
 * @method static \MStaack\LaravelPostgis\Eloquent\Builder|Stats whereLinestring($value)
 * @method static \MStaack\LaravelPostgis\Eloquent\Builder|Stats whereMaxAltitude($value)
 * @method static \MStaack\LaravelPostgis\Eloquent\Builder|Stats whereMaxHeartrate($value)
 * @method static \MStaack\LaravelPostgis\Eloquent\Builder|Stats whereMaxSpeed($value)
 * @method static \MStaack\LaravelPostgis\Eloquent\Builder|Stats whereMinAltitude($value)
 * @method static \MStaack\LaravelPostgis\Eloquent\Builder|Stats whereMovingTime($value)
 * @method static \MStaack\LaravelPostgis\Eloquent\Builder|Stats whereStartPoint($value)
 * @method static \MStaack\LaravelPostgis\Eloquent\Builder|Stats whereStartedAt($value)
 * @method static \MStaack\LaravelPostgis\Eloquent\Builder|Stats whereStatsId($value)
 * @method static \MStaack\LaravelPostgis\Eloquent\Builder|Stats whereStatsType($value)
 * @method static \MStaack\LaravelPostgis\Eloquent\Builder|Stats whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Stats extends Model
{
    use HasFactory, HasAdditionalData, PostgisTrait;

    protected $appends = [
        'human_started_at',
        'human_ended_at',
    ];

    protected $hidden = [
        'time_data',
        'cadence_data',
        'temperature_data',
        'heart_rate_data',
        'speed_data',
        'grade_data',
        'battery_data',
        'calories_data',
        'cumulative_distance_data',
    ];

    protected $fillable = [
        'linestring',

        'integration',

        'activity_id',

        // Distance in metres
        'distance',
        // Date and time the ride was started
        'started_at',
        // Date and time the ride was ended
        'finished_at',
        // The duration of the ride in seconds
        'duration',
        // The average speed of the ride in metres per second
        'average_speed',
        // The average pace of the ride in seconds per metre
        'average_pace',
        // The minimum altitude in metres
        'min_altitude',
        // The maximum altitude in metres
        'max_altitude',
        // The cumulative elevation gained in metres
        'elevation_gain',
        // The cumulative elevation lost in metres
        'elevation_loss',
        // The moving time in seconds
        'moving_time',
        // The maximum speed in metres per second
        'max_speed',
        // The average cadence in rpm
        'average_cadence',
        // The average temperature in deg C
        'average_temp',
        // The average watts in W
        'average_watts',
        // The average energy output in kjoules
        'kilojoules',
        // The starting point
        'start_point',
        // The end point
        'end_point',
        // The average heartrate in bpm
        'average_heartrate',
        // The max heartrate in bpm
        'max_heartrate',
        // The number of calories burned
        'calories',

        'time_data',
        'cadence_data',
        'temperature_data',
        'heart_rate_data',
        'speed_data',
        'grade_data',
        'battery_data',
        'calories_data',
        'cumulative_distance_data',
    ];

    protected $casts = [
        'distance' => 'float',
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
        'duration' => 'float',
        'average_speed' => 'float',
        'average_pace' => 'float',
        'min_altitude' => 'float',
        'max_altitude' => 'float',
        'elevation_gain' => 'float',
        'elevation_loss' => 'float',
        'moving_time' => 'float',
        'max_speed' => 'float',
        'average_cadence' => 'float',
        'average_temp' => 'float',
        'average_watts' => 'float',
        'kilojoules' => 'float',
        'max_heartrate' => 'float',
        'average_heartrate' => 'float',
        'calories' => 'float',
        'time_data' => 'array',
        'cadence_data' => 'array',
        'temperature_data' => 'array',
        'heart_rate_data' => 'array',
        'speed_data' => 'array',
        'grade_data' => 'array',
        'battery_data' => 'array',
        'calories_data' => 'array',
        'cumulative_distance_data' => 'array',
    ];

    protected $postgisFields = [
        'linestring',
        'start_point',
        'end_point',
    ];

    protected $postgisTypes = [
        'linestring' => [
            'geomtype' => 'geography',
            'srid' => 4326,
        ],
        'start_point' => [
            'geomtype' => 'geography',
            'srid' => 4326,
        ],
        'end_point' => [
            'geomtype' => 'geography',
            'srid' => 4326,
        ],
    ];

    final public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }

    protected static function booted()
    {
        static::created(function (Stats $stats) {
            if ($stats->linestring !== null) {
                GenerateActivityThumbnail::dispatch($stats);
            }
        });
        static::saved(function (Stats $stats) {
            if ($stats->wasChanged('linestring')) {
                GenerateActivityThumbnail::dispatch($stats);
            }
        });
    }

    public function activity(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Activity::class, 'activity_id');
    }

    public function getHumanStartedAtAttribute()
    {
        if (!$this->start_point) {
            return null;
        }

        return app(Geocoder::class)->getPlaceSummaryFromPosition($this->start_point->getLat(), $this->start_point->getLng());
    }

    public function getHumanEndedAtAttribute()
    {
        if (!$this->end_point) {
            return null;
        }

        return app(Geocoder::class)->getPlaceSummaryFromPosition($this->end_point->getLat(), $this->end_point->getLng());
    }

    public function getLinestringWithDistanceAttribute()
    {
        if ($this->linestring === null) {
            return null;
        }
        $cumulativeDistancePoints = collect($this->cumulative_distance_data);
        if ($cumulativeDistancePoints->count() < $this->linestring->count()) {
            $cumulativeDistancePoints = $cumulativeDistancePoints->merge(array_fill(0, $this->linestring->count() - $cumulativeDistancePoints->count(), 0));
        }

        return collect($this->linestring->getPoints())
            ->map(fn (Point $point, int $index) => [$point->getLng(), $point->getLat(), $point->getAlt(), $cumulativeDistancePoints[$index]])
            ->toArray();
    }

    public static function default()
    {
        return new static();
    }

    public static function scopeOrderByPreference(Builder $query)
    {
        $order = collect(StatsOrder::getValue())
            ->map(fn (string $integration) => sprintf('\'%s\'', $integration))
            ->join(', ');

        return $query->orderByRaw(
            sprintf('array_position(ARRAY[%s]::varchar[], %sstats.integration) ASC', $order, DB::getTablePrefix() ?? '')
        );
    }

    public static function scopePreferred(Builder $query)
    {
        $query->orderByPreference();
    }
}
