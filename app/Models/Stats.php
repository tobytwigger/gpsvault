<?php

namespace App\Models;

use App\Services\Geocoding\Geocoder;
use App\Settings\StatsOrder;
use App\Traits\HasAdditionalData;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\DB;
use MStaack\LaravelPostgis\Eloquent\PostgisTrait;

class Stats extends Model
{
    use HasFactory, HasAdditionalData, PostgisTrait;

    protected $appends = [
        'human_started_at',
        'human_ended_at',
    ];

    protected $fillable = [
        'linestring',

        'integration',

        'stats_id',

        'stats_type',
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
    ];

    protected $postgisFields = [
        'linestring',
        'start_point',
        'end_point'
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
    }

    public function model(): MorphTo
    {
        return $this->morphTo('stats');
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

    public function activityPoints()
    {
        return $this->hasMany(ActivityPoint::class);
    }
}
