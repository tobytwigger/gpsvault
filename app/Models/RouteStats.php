<?php

namespace App\Models;

use App\Traits\HasAdditionalData;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class RouteStats extends Model
{
    use HasFactory, HasAdditionalData;

    protected $appends = [
        'human_started_at',
        'human_ended_at',
    ];

    protected $fillable = [
        'integration',

        'route_id',
        // Distance in metres
        'distance',
        // The minimum altitude in metres
        'min_altitude',
        // The maximum altitude in metres
        'max_altitude',
        // The cumulative elevation gained in metres
        'elevation_gain',
        // The cumulative elevation lost in metres
        'elevation_loss',
        // The start latitude
        'start_latitude',
        // The start longitude
        'start_longitude',
        // The end latitude
        'end_latitude',
        // The end longitude
        'end_longitude',
        // A file that contains the points as json
        'json_points_file_id'
    ];

    protected $casts = [
        'distance' => 'float',
        'min_altitude' => 'float',
        'max_altitude' => 'float',
        'elevation_gain' => 'float',
        'elevation_loss' => 'float',
        'start_latitude' => 'float',
        'start_longitude' => 'float',
        'end_latitude' => 'float',
        'end_longitude' => 'float',
    ];

    protected static function booted()
    {
        static::created(function(RouteStats $routeStats) {
            if($routeStats->route->distance === null && $routeStats->distance !== null) {
                $routeStats->route()->update(['distance' => $routeStats->distance]);
            }
        });
    }

    private function getResults($lat, $lon)
    {
        $result = app('nominatim')->find(
            app('nominatim')->newReverse()->latlon($lat, $lon)
        );
        if(array_key_exists('address', $result)) {
            $address = Arr::only($result['address'], ['town', 'city', 'county', 'state_district', 'state', 'country']);
            return join(', ', array_slice($address, 0, 4));
        }
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

    public function route()
    {
        return $this->belongsTo(Route::class);
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
