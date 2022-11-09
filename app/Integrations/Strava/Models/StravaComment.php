<?php

namespace App\Integrations\Strava\Models;

use App\Models\Activity;
use Database\Factories\StravaCommentsFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Integrations\Strava\Models\StravaComment
 *
 * @property int $id
 * @property int $strava_id
 * @property string $first_name
 * @property string $last_name
 * @property string $text
 * @property \Illuminate\Support\Carbon $posted_at
 * @property int $activity_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Activity $activity
 * @property-read mixed $name
 * @method static \Database\Factories\StravaCommentsFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|StravaComment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StravaComment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StravaComment query()
 * @method static \Illuminate\Database\Eloquent\Builder|StravaComment whereActivityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StravaComment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StravaComment whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StravaComment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StravaComment whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StravaComment wherePostedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StravaComment whereStravaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StravaComment whereText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StravaComment whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class StravaComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name', 'last_name', 'activity_id', 'text', 'posted_at', 'strava_id',
    ];

    protected $casts = [
        'posted_at' => 'datetime',
        'strava_id' => 'integer',
    ];

    protected $appends = ['name'];

    public function getNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }

    protected static function newFactory()
    {
        return new StravaCommentsFactory();
    }
}
