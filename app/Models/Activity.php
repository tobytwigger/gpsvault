<?php

namespace App\Models;

use App\Services\ActivityData\ActivityData;
use App\Services\ActivityData\Analysis;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use function Illuminate\Events\queueable;

class Activity extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'description', 'filepath', 'type', 'distance', 'start_at'
    ];

    protected $casts = [
        'distance' => 'float',
        'start_at' => 'datetime'
    ];

    protected static function booted()
    {
        static::created(queueable(function(Activity $activity) {
            $data = $activity->getActivityData();
            $activity->distance = $data->getDistance();
            $activity->start_at = $data->getStartedAt();
            $activity->save();
        }));
    }

    public function getDataAttribute()
    {
        return $this->getActivityData();
    }

    public function getActivityData(): Analysis
    {
        return ActivityData::analyse($this);
    }
}
