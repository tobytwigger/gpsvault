<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

/**
 * App\Models\RoutePathWaypoint.
 *
 * @property int $id
 * @property int $route_path_id
 * @property int $waypoint_id
 * @property int $order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\RoutePath $routePath
 * @property-read \App\Models\Waypoint $waypoint
 * @method static \Illuminate\Database\Eloquent\Builder|RoutePathWaypoint newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RoutePathWaypoint newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RoutePathWaypoint ordered(string $direction = 'asc')
 * @method static \Illuminate\Database\Eloquent\Builder|RoutePathWaypoint query()
 * @method static \Illuminate\Database\Eloquent\Builder|RoutePathWaypoint whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RoutePathWaypoint whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RoutePathWaypoint whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RoutePathWaypoint whereRoutePathId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RoutePathWaypoint whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RoutePathWaypoint whereWaypointId($value)
 * @mixin \Eloquent
 */
class RoutePathWaypoint extends Model implements Sortable
{
    use SortableTrait;

    protected $fillable = [
        'order', 'route_path_id', 'waypoint_id',
    ];

    protected $sortable = [
        'order_column_name' => 'order',
        'sort_when_creating' => true,
    ];

    public function routePath()
    {
        return $this->belongsTo(RoutePath::class);
    }

    public function waypoint()
    {
        return $this->belongsTo(Waypoint::class);
    }

    public function buildSortQuery()
    {
        return static::query()->where('route_path_id', $this->route_path_id);
    }
}
