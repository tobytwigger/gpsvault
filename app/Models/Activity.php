<?php

namespace App\Models;

use App\Integrations\Strava\Models\StravaComment;
use App\Integrations\Strava\Models\StravaKudos;
use App\Jobs\AnalyseActivityFile;
use App\Traits\HasAdditionalData;
use Database\Factories\ActivityFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use function Illuminate\Events\queueable;

class Activity extends Model
{
    use HasFactory, HasAdditionalData;

    protected $fillable = [
        'name', 'description', 'activity_file_id', 'linked_to', 'user_id', 'distance', 'started_at'
    ];

    protected $with = [
        'stravaComments', 'stravaKudos'
    ];

    protected $casts = [
        'linked_to' => 'array',
        'user_id' => 'integer',
        'distance' => 'float',
        'started_at' => 'datetime'
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

    public function getStatsAsArray(bool $withPoints = false)
    {
        return $this->activityStats()->get()->mapWithKeys(function(ActivityStats $stats) use ($withPoints) {
            if($withPoints) {
                $stats->append('points');
            }
            return [$stats->integration => $stats->toArray()];
        });
    }

    public function getStatsAttribute()
    {
        return $this->getStatsAsArray(false);
    }

    public function activityStatsFrom(string $integration)
    {
        return $this->activityStats()->whereIntegration($integration);
    }

    public function activityStats()
    {
        return $this->hasMany(ActivityStats::class);
    }

    public function defaultStats(bool $withPoints = false)
    {
        $stats = $this->getStatsAsArray($withPoints);
        if(count($stats) === 0) {
            throw new \Exception('There is no position information for this activity', 404);
        }
        if(array_key_exists($stats['php'])) {
            return $stats['php'];
        }
        return Arr::first($stats);
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
            $activity->activityStats()->delete();
            $activity->stravaComments()->delete();
            $activity->stravaKudos()->delete();
        }));

        static::saved(function(Activity $activity) {
            if ($activity->isDirty('activity_file_id') && $activity->hasActivityFile()) {
                $activity->refresh();
                AnalyseActivityFile::dispatch($activity);
            }
        });
    }

    public function hasActivityFile(): bool
    {
        return $this->activityFile()->exists();
    }

    protected static function newFactory()
    {
        return new ActivityFactory();
    }
}
