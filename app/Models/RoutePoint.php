<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use MStaack\LaravelPostgis\Eloquent\PostgisTrait;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

class RoutePoint extends Model implements Sortable
{
    use HasFactory, PostgisTrait, SortableTrait;

    protected $fillable = [
        'order', 'place_id', 'location', 'route_path_id',
    ];

    protected $sortable = [
        'order_column_name' => 'order',
        'sort_when_creating' => true,
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

    public function routePath()
    {
        return $this->belongsTo(RoutePath::class);
    }

    public function place()
    {
        return $this->belongsTo(Place::class);
    }
}
