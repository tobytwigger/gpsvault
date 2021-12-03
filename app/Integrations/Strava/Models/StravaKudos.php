<?php

namespace App\Integrations\Strava\Models;

use App\Models\Activity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StravaKudos extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name', 'last_name', 'activity_id'
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

}
