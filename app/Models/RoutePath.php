<?php

namespace App\Models;

use App\Jobs\GenerateRouteThumbnail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use MStaack\LaravelPostgis\Eloquent\PostgisTrait;
use MStaack\LaravelPostgis\Geometries\LineString;

/**
 * @property LineString $linestring The linestring representing the route
 */
class RoutePath extends Model
{
    use HasFactory, PostgisTrait;

    protected $fillable = [
        'linestring', 'distance', 'elevation_gain', 'route_id', 'duration', 'settings', 'thumbnail_id'
    ];

    protected $casts = [
        'distance' => 'float',
        'elevation' => 'float',
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
