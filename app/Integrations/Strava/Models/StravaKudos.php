<?php

namespace App\Integrations\Strava\Models;

use App\Models\Activity;
use Database\Factories\StravaKudosFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Integrations\Strava\Models\StravaKudos
 *
 * @property int $id
 * @property string $first_name
 * @property string $last_name
 * @property int $activity_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Activity $activity
 * @property-read mixed $name
 * @method static \Database\Factories\StravaKudosFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|StravaKudos newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StravaKudos newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StravaKudos query()
 * @method static \Illuminate\Database\Eloquent\Builder|StravaKudos whereActivityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StravaKudos whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StravaKudos whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StravaKudos whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StravaKudos whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StravaKudos whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class StravaKudos extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name', 'last_name', 'activity_id',
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
        return new StravaKudosFactory();
    }
}
