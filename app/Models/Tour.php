<?php

namespace App\Models;

use App\Services\Geocoding\Geocoder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

/**
 * App\Models\Tour
 *
 * @property int $id
 * @property int $user_id
 * @property string|null $name
 * @property string|null $description
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $marked_as_started_at
 * @property \Illuminate\Support\Carbon|null $marked_as_finished_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $distance
 * @property-read mixed $elevation_gain
 * @property-read mixed $human_ended_at
 * @property-read mixed $human_started_at
 * @method static \Database\Factories\TourFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Tour newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Tour newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Tour query()
 * @method static \Illuminate\Database\Eloquent\Builder|Tour whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tour whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tour whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tour whereMarkedAsFinishedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tour whereMarkedAsStartedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tour whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tour whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tour whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tour whereUserId($value)
 * @mixin \Eloquent
 */
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
                $distance += $stage->route->path->distance;
            }
        }

        return $distance;
    }

    public function getElevationGainAttribute()
    {
        $elevation = 0;
        foreach ($this->stages as $stage) {
            if ($stage->route_id && $stage->route?->path->elevation_gain) {
                $elevation += $stage->route->path->elevation_gain;
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
