<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use MStaack\LaravelPostgis\Eloquent\PostgisTrait;

class RoutePoint extends Model
{
    use HasFactory, PostgisTrait;

    protected $fillable = [
        'order', 'place_id', 'location', 'route_id',
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

    public function route()
    {
        return $this->belongsTo(Route::class);
    }

    public function place()
    {
        return $this->belongsTo(Place::class);
    }
}
