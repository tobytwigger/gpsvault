<?php

namespace App\Models;

use App\Jobs\GenerateRouteThumbnail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use MStaack\LaravelPostgis\Eloquent\PostgisTrait;
use MStaack\LaravelPostgis\Geometries\LineString;

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
        'linestring', 'distance', 'elevation_gain', 'route_id', 'duration', 'settings', 'thumbnail_id',
    ];

    protected $casts = [
        'distance' => 'float',
        'elevation_gain' => 'float',
        'duration' => 'float',
        'settings' => 'array',
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

    public function routePathWaypoints()
    {
        return $this->hasMany(RoutePathWaypoint::class);
    }

    public function getWaypointsAttribute()
    {
        return $this->routePathWaypoints()->ordered()->with('waypoint')->get()
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
}
