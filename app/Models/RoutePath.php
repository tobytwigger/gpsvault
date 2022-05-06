<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use MStaack\LaravelPostgis\Eloquent\PostgisTrait;

class RoutePath extends Model
{
    use HasFactory, PostgisTrait;

    protected $fillable = [
        'linestring', 'distance', 'elevation', 'route_id',
    ];

    protected $casts = [
        'distance' => 'float',
        'elevation' => 'float'
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

    public function route()
    {
        return $this->belongsTo(Route::class);
    }
}
