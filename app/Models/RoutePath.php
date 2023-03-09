<?php

namespace App\Models;

use App\Jobs\GenerateRouteThumbnail;
use App\Services\Geocoding\Geocoder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use MStaack\LaravelPostgis\Eloquent\PostgisTrait;
use MStaack\LaravelPostgis\Geometries\LineString;
use MStaack\LaravelPostgis\Geometries\Point;

/**
 * App\Models\RoutePath.
 *
 * @property LineString $linestring The linestring representing the route
 * @property int $id
 * @property float $distance
 * @property float $elevation_gain
 * @property float $duration
 * @property array|null $settings
 * @property int $route_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $thumbnail_id
 * @property-read mixed $waypoints
 * @property-read \App\Models\Route $route
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\RoutePathWaypoint[] $routePathWaypoints
 * @property-read int|null $route_path_waypoints_count
 * @property-read \App\Models\File|null $thumbnail
 * @method static \Database\Factories\RoutePathFactory factory(...$parameters)
 * @method static \MStaack\LaravelPostgis\Eloquent\Builder|RoutePath newModelQuery()
 * @method static \MStaack\LaravelPostgis\Eloquent\Builder|RoutePath newQuery()
 * @method static \MStaack\LaravelPostgis\Eloquent\Builder|RoutePath query()
 * @method static \MStaack\LaravelPostgis\Eloquent\Builder|RoutePath whereCreatedAt($value)
 * @method static \MStaack\LaravelPostgis\Eloquent\Builder|RoutePath whereDistance($value)
 * @method static \MStaack\LaravelPostgis\Eloquent\Builder|RoutePath whereDuration($value)
 * @method static \MStaack\LaravelPostgis\Eloquent\Builder|RoutePath whereElevationGain($value)
 * @method static \MStaack\LaravelPostgis\Eloquent\Builder|RoutePath whereId($value)
 * @method static \MStaack\LaravelPostgis\Eloquent\Builder|RoutePath whereLinestring($value)
 * @method static \MStaack\LaravelPostgis\Eloquent\Builder|RoutePath whereRouteId($value)
 * @method static \MStaack\LaravelPostgis\Eloquent\Builder|RoutePath whereSettings($value)
 * @method static \MStaack\LaravelPostgis\Eloquent\Builder|RoutePath whereThumbnailId($value)
 * @method static \MStaack\LaravelPostgis\Eloquent\Builder|RoutePath whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class RoutePath extends Model
{
    use HasFactory, PostgisTrait;

    protected $fillable = [
        'linestring', 'distance', 'elevation_gain', 'route_id', 'duration', 'settings', 'thumbnail_id', 'cumulative_distance',
    ];

    protected $casts = [
        'distance' => 'float',
        'elevation_gain' => 'float',
        'duration' => 'float',
        'settings' => 'array',
        'cumulative_distance' => 'array',
    ];

    protected $appends = [
        'human_started_at', 'human_ended_at', 'linestring_with_distance',
    ];

    protected $postgisFields = [
        'linestring',
    ];

    protected $postgisTypes = [
        'linestring' => [
            'geomtype' => 'geography',
            'srid' => 4326,
        ],
    ];

    protected static function booted()
    {
        static::created(function (RoutePath $routePath) {
            if ($routePath->linestring !== null) {
                GenerateRouteThumbnail::dispatch($routePath);
            }
        });
        static::saved(function (RoutePath $routePath) {
            if ($routePath->wasChanged('linestring')) {
                GenerateRouteThumbnail::dispatch($routePath);
            }
        });
    }

    public function getLinestringWithDistanceAttribute()
    {
        if ($this->linestring === null) {
            return null;
        }
        $cumulativeDistancePoints = collect($this->cumulative_distance);
        if ($cumulativeDistancePoints->count() < $this->linestring->count()) {
            $cumulativeDistancePoints = $cumulativeDistancePoints->merge(array_fill(0, $this->linestring->count() - $cumulativeDistancePoints->count(), null));
        }

        return collect($this->linestring->getPoints())
            ->map(fn (Point $point, int $index) => [$point->getLng(), $point->getLat(), $point->getAlt(), $cumulativeDistancePoints[$index]])
            ->toArray();
    }

    public function routePathWaypoints()
    {
        return $this->hasMany(RoutePathWaypoint::class);
    }

    public function getWaypointsAttribute()
    {
        return $this->routePathWaypoints()->ordered()->with('waypoint', 'waypoint.place')->get()
            ->map(fn (RoutePathWaypoint $pivot) => $pivot->waypoint);
    }

    public function route()
    {
        return $this->belongsTo(Route::class);
    }

    public function thumbnail()
    {
        return $this->belongsTo(File::class, 'thumbnail_id');
    }

    public function getHumanStartedAtAttribute()
    {
        return app(Geocoder::class)->getPlaceSummaryFromPosition($this->linestring->getPoints()[0]->getLat(), $this->linestring->getPoints()[0]->getLng());
    }

    public function getHumanEndedAtAttribute()
    {
        return app(Geocoder::class)->getPlaceSummaryFromPosition(Arr::last($this->linestring->getPoints())->getLat(), Arr::last($this->linestring->getPoints())->getLng());
    }
}
