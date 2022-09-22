<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use MStaack\LaravelPostgis\Eloquent\PostgisTrait;

class Waypoint extends Model
{
    use HasFactory, PostgisTrait;

    protected $fillable = [
        'place_id', 'location', 'name', 'notes',
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
}
