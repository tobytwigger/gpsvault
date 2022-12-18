<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use MStaack\LaravelPostgis\Eloquent\PostgisTrait;

/**
 * App\Models\Waypoint.
 *
 * @property int $id
 * @property int|null $place_id
 * @property mixed|null $location
 * @property string|null $name
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $user_id
 * @property-read \App\Models\Place|null $place
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\RoutePathWaypoint[] $routePathWaypoints
 * @property-read int|null $route_path_waypoints_count
 * @method static \Database\Factories\WaypointFactory factory(...$parameters)
 * @method static \MStaack\LaravelPostgis\Eloquent\Builder|Waypoint newModelQuery()
 * @method static \MStaack\LaravelPostgis\Eloquent\Builder|Waypoint newQuery()
 * @method static \MStaack\LaravelPostgis\Eloquent\Builder|Waypoint query()
 * @method static \MStaack\LaravelPostgis\Eloquent\Builder|Waypoint whereCreatedAt($value)
 * @method static \MStaack\LaravelPostgis\Eloquent\Builder|Waypoint whereId($value)
 * @method static \MStaack\LaravelPostgis\Eloquent\Builder|Waypoint whereLocation($value)
 * @method static \MStaack\LaravelPostgis\Eloquent\Builder|Waypoint whereName($value)
 * @method static \MStaack\LaravelPostgis\Eloquent\Builder|Waypoint whereNotes($value)
 * @method static \MStaack\LaravelPostgis\Eloquent\Builder|Waypoint wherePlaceId($value)
 * @method static \MStaack\LaravelPostgis\Eloquent\Builder|Waypoint whereUpdatedAt($value)
 * @method static \MStaack\LaravelPostgis\Eloquent\Builder|Waypoint whereUserId($value)
 * @mixin \Eloquent
 */
class Waypoint extends Model
{
    use HasFactory, PostgisTrait;

    protected $fillable = [
        'place_id', 'location', 'name', 'notes', 'user_id',
    ];

    protected $postgisFields = [
        'location',
    ];

    protected $postgisTypes = [
        'location' => [
            'geomtype' => 'geography',
            'srid' => 4326,
        ],
    ];

    public function place()
    {
        return $this->belongsTo(Place::class);
    }

    public function routePathWaypoints()
    {
        return $this->hasMany(RoutePathWaypoint::class);
    }
}
