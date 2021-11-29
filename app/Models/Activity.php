<?php

namespace App\Models;

use App\Services\ActivityData\ActivityData;
use App\Services\ActivityData\Analysis;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use function Illuminate\Events\queueable;

class Activity extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'description', 'activity_file_id', 'type', 'distance', 'start_at', 'linked_to', 'user_id'
    ];

    protected $casts = [
        'distance' => 'float',
        'start_at' => 'datetime',
        'linked_to' => 'array',
        'user_id' => 'integer'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function activityFile()
    {
        return $this->belongsTo(File::class, 'activity_file_id');
    }

    public function files()
    {
        return $this->belongsToMany(File::class);
    }

    public function additionalActivityData()
    {
        return $this->hasMany(AdditionalActivityData::class);
    }

    public function addOrUpdateAdditionalData(string $key, mixed $value): void
    {
        $this->additionalActivityData()->updateOrCreate(
            ['key' => $key, 'activity_id' => $this->id],
            ['value' => $value]
        );
    }

    public function getAdditionalData(string $key, $default = null): mixed
    {
        if($this->hasAdditionalData($key)) {
            return $this->additionalActivityData()->where('key', $key)->get()->value;
        }
        return $default;
    }

    public function hasAdditionalData(string $key): bool
    {
        return $this->additionalActivityData()->where(['key' => $key])->exists();
    }

    public function scopeWhereAdditionalDataContains(Builder $query, string $key, $value)
    {
        $query->whereHas(
            'additionalActivityData',
            fn(Builder $subQuery) => $subQuery->where('key', $key)->where('value', $value)
        );
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
        static::creating(function(Activity $activity) {
            if($activity->user_id === null) {
                $activity->user_id = Auth::id();
            }
        });
        static::deleted(queueable(function(Activity $activity) {
            $activity->activityFile()->delete();
            $activity->files()->delete();
        }));

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
