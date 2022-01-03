<?php

namespace App\Integrations\Strava\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class StravaClient extends Model
{
    use HasFactory;

    protected $casts = [
        'client_id' => 'encrypted',
        'client_secret' => 'encrypted'
    ];

    protected static function booted()
    {
        static::creating(function (StravaClient $client) {
            if ($client->user_id === null) {
                $client->user_id = Auth::id();
            }
        });
    }

    public function owner()
    {
        return $this->belongsTo(User::class);
    }
}
