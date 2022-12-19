<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use MStaack\LaravelPostgis\Eloquent\PostgisTrait;

/**
 * App\Models\ActivityPoint.
 *
 * @property int $id
 * @property mixed|null $points
 * @property float|null $elevation
 * @property string|null $time
 * @property float|null $cadence
 * @property float|null $temperature
 * @property float|null $heart_rate
 * @property float|null $speed
 * @property float|null $grade
 * @property float|null $battery
 * @property float|null $calories
 * @property float|null $cumulative_distance
 * @property int|null $order
 * @property int $stats_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $latitude
 * @property-read mixed $longitude
 * @property-read \App\Models\Stats $stats
 * @method static \MStaack\LaravelPostgis\Eloquent\Builder|ActivityPoint newModelQuery()
 * @method static \MStaack\LaravelPostgis\Eloquent\Builder|ActivityPoint newQuery()
 * @method static \MStaack\LaravelPostgis\Eloquent\Builder|ActivityPoint query()
 * @method static \MStaack\LaravelPostgis\Eloquent\Builder|ActivityPoint whereBattery($value)
 * @method static \MStaack\LaravelPostgis\Eloquent\Builder|ActivityPoint whereCadence($value)
 * @method static \MStaack\LaravelPostgis\Eloquent\Builder|ActivityPoint whereCalories($value)
 * @method static \MStaack\LaravelPostgis\Eloquent\Builder|ActivityPoint whereCreatedAt($value)
 * @method static \MStaack\LaravelPostgis\Eloquent\Builder|ActivityPoint whereCumulativeDistance($value)
 * @method static \MStaack\LaravelPostgis\Eloquent\Builder|ActivityPoint whereElevation($value)
 * @method static \MStaack\LaravelPostgis\Eloquent\Builder|ActivityPoint whereGrade($value)
 * @method static \MStaack\LaravelPostgis\Eloquent\Builder|ActivityPoint whereHeartRate($value)
 * @method static \MStaack\LaravelPostgis\Eloquent\Builder|ActivityPoint whereId($value)
 * @method static \MStaack\LaravelPostgis\Eloquent\Builder|ActivityPoint whereOrder($value)
 * @method static \MStaack\LaravelPostgis\Eloquent\Builder|ActivityPoint wherePoints($value)
 * @method static \MStaack\LaravelPostgis\Eloquent\Builder|ActivityPoint whereSpeed($value)
 * @method static \MStaack\LaravelPostgis\Eloquent\Builder|ActivityPoint whereStatsId($value)
 * @method static \MStaack\LaravelPostgis\Eloquent\Builder|ActivityPoint whereTemperature($value)
 * @method static \MStaack\LaravelPostgis\Eloquent\Builder|ActivityPoint whereTime($value)
 * @method static \MStaack\LaravelPostgis\Eloquent\Builder|ActivityPoint whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ActivityPoint extends Model
{
    use PostgisTrait;

    protected $table = 'activity_points';

    protected $postgisFields = [
        'points',
    ];

    protected $postgisTypes = [
        'points' => [
            'geomtype' => 'geography',
            'srid' => 4326,
        ],
    ];

    protected $fillable = [
        'points',
        'elevation',
        'time',
        'cadence',
        'temperature',
        'heart_rate',
        'speed',
        'grade',
        'battery',
        'calories',
        'cumulative_distance',
        'stats_id',
        'order',
    ];

    protected $casts = [
        'cumulative_distance' => 'float',
    ];

    public function stats()
    {
        return $this->belongsTo(Stats::class);
    }

    public function getLongitudeAttribute()
    {
        return $this->points->getLng();
    }

    public function getLatitudeAttribute()
    {
        return $this->points->getLat();
    }
}
