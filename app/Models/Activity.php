<?php

namespace App\Models;

use App\Integrations\Strava\Models\StravaComment;
use App\Integrations\Strava\Models\StravaKudos;
use App\Traits\HasAdditionalData;
use App\Traits\HasStats;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Laravel\Scout\Searchable;

class Activity extends Model
{
    use HasFactory, HasAdditionalData, HasStats, Searchable;

    protected $fillable = [
        'name', 'description', 'file_id', 'linked_to', 'user_id'
    ];

    protected $with = [
        'stravaComments', 'stravaKudos'
    ];

    protected $appends = [
        'cover_image', 'distance', 'started_at'
    ];

    protected $casts = [
        'linked_to' => 'array',
        'user_id' => 'integer',
    ];

    public function toSearchableArray()
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
            'user_id' => $this->user_id
        ];
    }

    protected static function booted()
    {
        static::creating(function (Activity $activity) {
            if ($activity->user_id === null) {
                $activity->user_id = Auth::id();
            }
        });
        static::deleting(function (Activity $activity) {
            $activity->file()->delete();
            $activity->files()->delete();
            $activity->stats()->delete();
            $activity->stravaComments()->delete();
            $activity->stravaKudos()->delete();
        });

    }

    public function getCoverImageAttribute()
    {
        $image = $this->files()->where('mimetype', 'LIKE', 'image/%')->first();
        if ($image) {
            return route('file.preview', $image);
        }
        return null;
    }

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

    public function files()
    {
        return $this->belongsToMany(File::class);
    }

    public function scopeLinkedTo(Builder $query, string $linkedTo)
    {
        $query->where('linked_to', 'LIKE', sprintf('%%%s%%', $linkedTo));
    }

    public function linkTo(string $integration)
    {
        $this->linked_to = array_unique(array_merge($this->linked_to, [$integration]));
        $this->save();
    }

}
