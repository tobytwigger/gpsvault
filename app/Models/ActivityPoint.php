<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use MStaack\LaravelPostgis\Eloquent\PostgisTrait;

class ActivityPoint extends Model
{
    use PostgisTrait;

    protected $table = 'activity_points';

    protected $postgisFields = [
        'points',
    ];

    protected $postgisTypes = [
        'points' => [
            'geomtype' => 'geography',
            'srid' => 4326,
        ],
    ];

    protected $fillable = [
        'points',
        'elevation',
        'time',
        'cadence',
        'temperature',
        'heart_rate',
        'speed',
        'grade',
        'battery',
        'calories',
        'cumulative_distance',
        'stats_id',
        'order'
    ];

    protected $casts = [

    ];

    public function stats()
    {
        return $this->belongsTo(Stats::class);
    }

    public function getLongitudeAttribute()
    {
        return $this->points->getLng();
    }

    public function getLatitudeAttribute()
    {
        return $this->points->getLat();
    }
}
