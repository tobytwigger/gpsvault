<?php

namespace App\Integrations\Strava\Models;

use App\Integrations\Strava\StravaToken;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class StravaClient extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'client_secret'
    ];

    protected $hidden = [
        'client_id', 'client_secret'
    ];

    protected $appends = [
        'is_connected'
    ];

    protected $casts = [
        'client_id' => 'encrypted',
        'client_secret' => 'encrypted',
        'webhook_verify_token' => 'encrypted',
        'used_15_min_calls' => 'integer',
        'used_daily_min_calls' => 'integer',
        '15_mins_resets_at' => 'datetime',
        'daily_resets_at' => 'datetime'
    ];

    protected static function booted()
    {
        static::creating(function (StravaClient $client) {
            if ($client->user_id === null) {
                $client->user_id = Auth::id();
            }
            if($client->webhook_verify_token === null) {
                $client->webhook_verify_token = Str::random(20);
            }
        });
    }

    public function getIsConnectedAttribute(): bool
    {
        if(Auth::check()) {
            return $this->tokens()->forUser(Auth::id())->count() > 0;
        }
        return false;
    }

    public function tokens()
    {
        return $this->hasMany(StravaToken::class);
    }

    public function redirectUrl(): string
    {
        return route('strava.callback', ['client' => $this->id]);
    }

    public static function scopeForUser(Builder $query, int $userId)
    {
        $query->where('user_id', $userId);
    }

    public function owner()
    {
        return $this->belongsTo(User::class);
    }

}
