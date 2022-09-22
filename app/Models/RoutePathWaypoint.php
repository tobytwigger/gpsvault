<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

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
