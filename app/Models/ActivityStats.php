<?php

namespace App\Models;

use App\Traits\HasAdditionalData;
use Database\Factories\ActivityStatsFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityStats extends Model
{
    use HasFactory, HasAdditionalData;

    protected $appends = [
        'is_power_data_available',
        'is_heartrate_data_available',
        'is_position_data_available',
        'is_temperature_data_available',
        'is_cadence_data_available',
        'is_speed_data_available',
        'is_time_data_available',
        'is_elevation_data_available',
    ];

    protected $fillable = [
        'integration',

        'activity_id',
        // Distance in metres
        'distance',
        // Date and time the ride was started
        'started_at',
        // Date and time the ride was ended
        'finished_at',
        // The duration of the ride in seconds
        'duration',
        // The average speed of the ride in metres per second
        'average_speed',
        // The average pace of the ride in seconds per metre
        'average_pace',
        // The minimum altitude in metres
        'min_altitude',
        // The maximum altitude in metres
        'max_altitude',
        // The cumulative elevation gained in metres
        'elevation_gain',
        // The cumulative elevation lost in metres
        'elevation_loss',
        // The moving time in seconds
        'moving_time',
        // The maximum speed in metres per second
        'max_speed',
        // The average cadence in rpm
        'average_cadence',
        // The average temperature in deg C
        'average_temp',
        // The average watts in W
        'average_watts',
        // The average energy output in kjoules
        'kilojoules',
        // The start latitude
        'start_latitude',
        // The start longitude
        'start_longitude',
        // The end latitude
        'end_latitude',
        // The end longitude
        'end_longitude',
        // The average heartrate in bpm
        'average_heartrate',
        // The max heartrate in bpm
        'max_heartrate',
        // The number of calories burned
        'calories',
        // A file that contains the points as json
        'json_points_file_id'
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
        'max_speed' => 'float',
        'average_cadence' => 'float',
        'average_temp' => 'float',
        'average_watts' => 'float',
        'kilojoules' => 'float',
        'start_latitude' => 'float',
        'start_longitude' => 'float',
        'end_latitude' => 'float',
        'end_longitude' => 'float',
        'max_heartrate' => 'float',
        'average_heartrate' => 'float',
        'calories' => 'float'
    ];

    protected static function booted()
    {
        static::created(function(ActivityStats $activityStats) {
            if($activityStats->activity->distance === null && $activityStats->distance !== null) {
                $activityStats->activity()->update(['distance' => $activityStats->distance]);
            }
            if($activityStats->activity->started_at === null && $activityStats->started_at !== null) {
                $activityStats->activity()->update(['started_at' => $activityStats->started_at]);
            }
        });
    }

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

    public function jsonPointsFile()
    {
        return $this->belongsTo(File::class, 'json_points_file_id');
    }

    public function getPointsAttribute()
    {
        return $this->points();
    }

    public function points()
    {
        if($this->json_points_file_id === null) {
            return [];
        }
        return json_decode(gzuncompress($this->jsonPointsFile?->getFileContents()) ?? [], true);
    }

    public function getAvailableDataAttribute(): array
    {
        return [
            'power' => $this->is_power_data_available,
            'heartrate' => $this->is_heartrate_data_available,
            'position' => $this->is_position_data_available,
            'temperature' => $this->is_temperature_data_available,
            'cadence' => $this->is_cadence_data_available,
            'speed' => $this->is_speed_data_available,
            'time' => $this->is_time_data_available,
            'elevation' => $this->is_elevation_data_available,
        ];
    }

    public function getIsPowerDataAvailableAttribute(): bool
    {
        return $this->calories !== null && $this->kilojoules !== null && $this->average_watts !== null;
    }

    public function getIsHeartrateDataAvailableAttribute(): bool
    {
        return $this->max_heartrate !== null && $this->average_heartrate !== null;
    }

    public function getIsPositionDataAvailableAttribute(): bool
    {
        return $this->start_latitude !== null && $this->start_longitude !== null && $this->end_latitude !== null
            && $this->end_longitude !== null && $this->distance !== null;
    }

    public function getIsTemperatureDataAvailableAttribute(): bool
    {
        return $this->average_temp !== null;
    }

    public function getIsCadenceDataAvailableAttribute(): bool
    {
        return $this->average_cadence !== null;
    }

    public function getIsSpeedDataAvailableAttribute(): bool
    {
        return $this->max_speed !== null && $this->average_speed !== null && $this->average_pace !== null;
    }

    public function getIsTimeDataAvailableAttribute(): bool
    {
        return $this->moving_time !== null && $this->duration !== null && $this->finished_at !== null
            && $this->started_at !== null;
    }

    public function getIsElevationDataAvailableAttribute(): bool
    {
        return $this->elevation_loss !== null && $this->elevation_gain !== null && $this->min_altitude !== null && $this->max_altitude !== null;
    }
}
