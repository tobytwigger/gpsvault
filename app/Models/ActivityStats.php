<?php

namespace App\Models;

use App\Traits\HasAdditionalData;
use Database\Factories\ActivityStatsFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityStats extends Model
{
    use HasFactory, HasAdditionalData;

    protected $fillable = [
        'integration',
        'activity_id',
        'distance',
        'started_at',
        'finished_at',
        'duration',
        'average_speed',
        'average_pace',
        'min_altitude',
        'max_altitude',
        'elevation_gain',
        'elevation_loss',
        'moving_time',
    ];

    protected $casts = [
        'distance' => 'float',
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
        'duration' => 'float',
        'average_speed' => 'float',
        'average_pace' => 'float',
        'min_altitude' => 'float',
        'max_altitude' => 'float',
        'elevation_gain' => 'float',
        'elevation_loss' => 'float',
        'moving_time' => 'float',
    ];

    public static function default()
    {
        return new static();
    }

    protected static function newFactory()
    {
        return new ActivityStatsFactory();
    }

    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }

}
