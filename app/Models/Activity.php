<?php

namespace App\Models;

use App\Integrations\Strava\Models\StravaComment;
use App\Integrations\Strava\Models\StravaKudos;
use App\Services\ActivityData\ActivityData;
use App\Services\ActivityData\Analysis;
use App\Traits\HasAdditionalData;
use Database\Factories\ActivityFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use function Illuminate\Events\queueable;

class Activity extends Model
{
    use HasFactory, HasAdditionalData;

    protected $fillable = [
        'name', 'description', 'activity_file_id', 'distance', 'start_at', 'linked_to', 'user_id'
    ];

    protected $with = [
        'stravaComments', 'stravaKudos'
    ];

    protected $casts = [
        'distance' => 'float',
        'start_at' => 'datetime',
        'linked_to' => 'array',
        'user_id' => 'integer'
    ];


    public function stravaComments()
    {
        return $this->hasMany(StravaComment::class);
    }

    public function stravaKudos()
    {
        return $this->hasMany(StravaKudos::class);
    }

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

//        static::saved(queueable(function(Activity $activity) {
//                if($activity->isDirty('activity_file_id') && $activity->hasActivityFile()) {
//                    $activity->refresh();
////                $data = $activity->getActivityData();
////                $activity->distance = $data->getDistance();
////                $activity->start_at = $data->getStartedAt();
////                $activity->save();
//                }
//        })->onQueue('stats'));
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

    protected static function newFactory()
    {
        return new ActivityFactory();
    }
}
