<?php

namespace App\Models;

use App\Services\ActivityData\ActivityData;
use App\Services\ActivityData\Analysis;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use function Illuminate\Events\queueable;

class Activity extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'description', 'activity_file_id', 'type', 'distance', 'start_at', 'additional_data', 'linked_to'
    ];

    protected $casts = [
        'distance' => 'float',
        'start_at' => 'datetime',
        'additional_data' => 'array',
        'linked_to' => 'array',
    ];

    public function activityFile()
    {
        return $this->belongsTo(File::class, 'activity_file_id');
    }

    public function files()
    {
        return $this->belongsToMany(File::class);
    }

    public function scopeWhereAdditionalDataContains(Builder $query, string $id, $value)
    {
        $query->where('additional_data', 'LIKE', sprintf('%%"%s":"%s"%%', $id, $value))
            ->orWhere('additional_data', 'LIKE', sprintf('%%"%s":%s%%', $id, $value));
    }

    public function scopeLinkedTo(Builder $query, string $linkedTo)
    {
        $query->where('linked_to', 'LIKE', sprintf('%%%s%%', $linkedTo));
    }

    public function scopeWithoutFile(Builder $query)
    {
        return $query->whereDoesntHave('activityFile');
    }

    protected static function booted()
    {
        static::created(queueable(function(Activity $activity) {
            if($activity->hasActivityFile()) {
                $data = $activity->getActivityData();
                $activity->distance = $data->getDistance();
                $activity->start_at = $data->getStartedAt();
                if($activity->name === null) {
                    if($activity->start_at !== null) {
                        $hour = $activity->start_at->format('H');
                        if ($hour < 12 && $hour > 4) {
                            $activity->name = 'Morning Ride';
                        }
                        if ($hour < 17) {
                            $activity->name = 'Afternoon Ride';
                        }
                        if ($hour < 23) {
                            $activity->name = 'Evening Ride';
                        }
                        $activity->name = 'Night Ride';
                    } else {
                        $activity->name = 'New Ride';
                    }
                }
                $activity->save();
            }
        }));
    }

    public function hasActivityFile(): bool
    {
        return $this->activityFile()->exists();
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
