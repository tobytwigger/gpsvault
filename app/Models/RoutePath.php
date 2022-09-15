<?php

namespace App\Models;

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
        'linestring', 'distance', 'elevation_gain', 'route_id', 'duration', 'settings'
    ];

    protected $casts = [
        'distance' => 'float',
        'elevation' => 'float',
        'duration' => 'float',
        'settings' => 'array'
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

    public function routePathWaypoints()
    {
        return $this->hasMany(RoutePathWaypoint::class);
    }

    public function getWaypointsAttribute()
    {
        return $this->routePathWaypoints()->ordered()->with('waypoint')->get()
            ->map(fn(RoutePathWaypoint $pivot) => $pivot->waypoint);
    }

    public function route()
    {
        return $this->belongsTo(Route::class);
    }
}
