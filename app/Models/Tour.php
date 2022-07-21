<?php

namespace App\Models;

use App\Services\Geocoding\Geocoder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
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

    public function stages(string $orderDirection = 'asc')
    {
        return $this->hasMany(Stage::class)->ordered($orderDirection);
    }

    public function getDistanceAttribute()
    {
        $distance = 0;
        foreach ($this->stages as $stage) {
            if ($stage->route_id && $stage->route->path?->distance) {
                $distance = $distance + $stage->route->path->distance;
            }
        }

        return $distance;
    }

    public function getElevationGainAttribute()
    {
        $elevation = 0;
        foreach ($this->stages as $stage) {
            if ($stage->route_id && $stage->route?->path->elevation_gain) {
                $elevation = $elevation + $stage->route->path->elevation_gain;
            }
        }

        return $elevation;
    }

    public function getHumanStartedAtAttribute()
    {
        $path = $this->stages()
            ->whereHas('route.routePaths')
            ->first()?->route?->path;
        if ($path && $path->linestring->count() > 1) {
            $point = Arr::first($path->linestring->getPoints());

            return app(Geocoder::class)->getPlaceSummaryFromPosition($point->getLat(), $point->getLng());
        }

        return null;
    }

    public function getHumanEndedAtAttribute()
    {
        $path = $this->stages('desc')
            ->whereHas('route.routePaths')
            ->first()?->route?->path;
        if ($path && $path->linestring->count() > 1) {
            $point = Arr::last($path->linestring->getPoints());

            return app(Geocoder::class)->getPlaceSummaryFromPosition($point->getLat(), $point->getLng());
        }

        return null;
    }
}
