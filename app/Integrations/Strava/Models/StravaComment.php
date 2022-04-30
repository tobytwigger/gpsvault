<?php

namespace App\Integrations\Strava\Models;

use App\Models\Activity;
use Database\Factories\StravaCommentsFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
