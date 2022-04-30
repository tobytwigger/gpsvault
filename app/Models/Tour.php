<?php

namespace App\Models;

use App\Services\Geocoding\Geocoder;
use App\Services\Stats\Addition\StatAdder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Tour extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'description', 'notes', 'marked_as_started_at', 'marked_as_finished_at',
    ];

    protected $casts = [
        'marked_as_started_at' => 'datetime',
        'marked_as_finished_at' => 'datetime',
    ];

    protected $appends = [
        'distance', 'elevation_gain',
        'human_started_at',
        'human_ended_at',
    ];

    protected static function booted()
    {
        static::creating(function (Tour $tour) {
            if ($tour->user_id === null) {
                $tour->user_id = Auth::id();
            }
        });
    }

    public function stages()
    {
        return $this->hasMany(Stage::class)->ordered();
    }

    private function getStatAdder(): StatAdder
    {
        $adder = new StatAdder();
        foreach ($this->stages as $stage) {
            if ($stage->route_id) {
                $stat = $stage->route->stats()->orderByPreference()->first();
                if ($stat) {
                    $adder->push($stat);
                }
            }
        }

        return $adder;
    }

    public function getStatsAttribute()
    {
        return $this->getStatAdder()->toArray();
    }

    public function getDistanceAttribute()
    {
        return $this->getStatAdder()->distance();
    }

    public function getElevationGainAttribute()
    {
        return $this->getStatAdder()->elevationGain();
    }

    public function getHumanStartedAtAttribute()
    {
        $latitude = $this->getStatAdder()->startLatitude();
        $longitude = $this->getStatAdder()->startLongitude();
        if ($latitude === null || $longitude === null) {
            return null;
        }

        return app(Geocoder::class)->getPlaceSummaryFromPosition($latitude, $longitude);
    }

    public function getHumanEndedAtAttribute()
    {
        $latitude = $this->getStatAdder()->endLatitude();
        $longitude = $this->getStatAdder()->endLongitude();

        if ($latitude === null || $longitude === null) {
            return null;
        }

        return app(Geocoder::class)->getPlaceSummaryFromPosition($latitude, $longitude);
    }
}
