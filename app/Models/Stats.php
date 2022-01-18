<?php

namespace App\Models;

use App\Services\File\FileUploader;
use App\Traits\HasAdditionalData;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stats extends Model
{
    use HasFactory, HasAdditionalData;

    protected $appends = [
        'human_started_at',
        'human_ended_at',
    ];

    protected $fillable = [
        'integration',

        'stats_id',

        'stats_type',
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
        static::created(function(Stats $stats) {
//            if($stats->file->type === FileUploader::ROUTE_FILE) {
//                Route::where('file_id', $stats->file->id)->first()?->notifyAboutNewStats($stats);
//            } elseif($stats->file->type === FileUploader::ACTIVITY_FILE) {
//                Activity::where('file_id', $stats->file->id)->first()?->notifyAboutNewStats($stats);
//            }
        });
    }

    public function model(): \Illuminate\Database\Eloquent\Relations\MorphTo
    {
        return $this->morphTo();
    }

    public function file()
    {
        return $this->belongsTo(File::class);
    }

    private function getResults($lat, $lon)
    {
//        $result = app('nominatim')->find(
//            app('nominatim')->newReverse()->latlon($lat, $lon)
//        );
//        if(array_key_exists('address', $result)) {
//            $address = Arr::only($result['address'], ['town', 'city', 'county', 'state_district', 'state', 'country']);
//            return join(', ', array_slice($address, 0, 4));
//        }
        return null;
    }

    public function getHumanStartedAtAttribute()
    {
        if(!$this->start_latitude || !$this->start_longitude) {
            return null;
        }

        return cache()->remember(
            'findlatlong-' . $this->start_latitude . $this->start_longitude,
            1000000,
            fn() => $this->getResults($this->start_latitude, $this->start_longitude)
        );
    }

    public function getHumanEndedAtAttribute()
    {
        if(!$this->end_latitude || !$this->end_longitude) {
            return null;
        }
        return cache()->remember(
            'findlatlong-' . $this->end_latitude . $this->end_longitude,
            1000000,
            fn() => $this->getResults($this->end_latitude, $this->end_longitude)
        );
    }

    public static function default()
    {
        return new static();
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

}
