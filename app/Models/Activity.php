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

/**
 * App\Models\Activity
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $description
 * @property int|null $file_id
 * @property array|null $linked_to
 * @property int|null $thumbnail_id
 * @property int $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\AdditionalData[] $additionalData
 * @property-read int|null $additional_data_count
 * @property-read \App\Models\File|null $file
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\File[] $files
 * @property-read int|null $files_count
 * @property-read mixed $additional_data
 * @property-read mixed $cover_image
 * @property-read int|null $distance
 * @property-read \Carbon\Carbon|null $started_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Stats[] $stats
 * @property-read int|null $stats_count
 * @property-read \Illuminate\Database\Eloquent\Collection|StravaComment[] $stravaComments
 * @property-read int|null $strava_comments_count
 * @property-read \Illuminate\Database\Eloquent\Collection|StravaKudos[] $stravaKudos
 * @property-read int|null $strava_kudos_count
 * @property-read \App\Models\File|null $thumbnail
 * @property-read \App\Models\User $user
 * @method static \Database\Factories\ActivityFactory factory(...$parameters)
 * @method static Builder|Activity linkedTo(string $linkedTo)
 * @method static Builder|Activity newModelQuery()
 * @method static Builder|Activity newQuery()
 * @method static Builder|Activity orderByStat(string $stat)
 * @method static Builder|Activity query()
 * @method static Builder|Activity whereAdditionalData(string $key, $value)
 * @method static Builder|Activity whereCreatedAt($value)
 * @method static Builder|Activity whereDescription($value)
 * @method static Builder|Activity whereFileId($value)
 * @method static Builder|Activity whereHasAdditionalData(string $key)
 * @method static Builder|Activity whereId($value)
 * @method static Builder|Activity whereLinkedTo($value)
 * @method static Builder|Activity whereName($value)
 * @method static Builder|Activity whereThumbnailId($value)
 * @method static Builder|Activity whereUpdatedAt($value)
 * @method static Builder|Activity whereUserId($value)
 * @method static Builder|Activity withoutFile()
 * @mixin \Eloquent
 */
class Activity extends Model
{
    use HasFactory, HasAdditionalData, HasStats, Searchable;

    protected $fillable = [
        'name', 'description', 'file_id', 'linked_to', 'user_id', 'thumbnail_id',
    ];

    protected $with = [
        'stravaComments', 'stravaKudos',
    ];

    protected $appends = [
        'cover_image', 'distance', 'started_at',
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
            'updated_at' => $this->updated_at,
            'user_id' => $this->user_id,
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
        if ($this->thumbnail_id !== null) {
            return route('file.preview', $this->thumbnail);
        }

        $image = $this->files()->where('mimetype', 'LIKE', 'image/%')->first();
        if ($image) {
            return route('file.preview', $image);
        }

        return null;
    }

    public function thumbnail()
    {
        return $this->belongsTo(File::class, 'thumbnail_id');
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
