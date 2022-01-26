<?php

namespace App\Integrations\Strava\Client\Authentication;

use App\Integrations\Strava\Client\Models\StravaClient;
use App\Models\User;
use Carbon\Carbon;
use Database\Factories\StravaTokenFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class StravaToken extends Model
{
    use HasFactory;

    protected $casts = [
        'expires_at' => 'datetime',
        'access_token' => 'encrypted',
        'refresh_token' => 'encrypted'
    ];

    protected $hidden = [
        'access_token',
        'refresh_token'
    ];

    protected $fillable = ['user_id', 'strava_client_id', 'access_token', 'refresh_token', 'expires_at', 'disabled'];

    public static function makeFromStravaTokenResponse(\App\Integrations\Strava\Client\Authentication\StravaTokenResponse $token, int $clientId)
    {
        $instance = new StravaToken();

        $instance->access_token = $token->getAccessToken();
        $instance->refresh_token = $token->getRefreshToken();
        $instance->expires_at = $token->getExpiresAt();
        $instance->user_id = Auth::id();
        $instance->disabled = false;
        $instance->strava_client_id = $clientId;

        return $instance;
    }

    public function updateFromStravaTokenResponse(\App\Integrations\Strava\Client\Authentication\StravaTokenResponse $token)
    {
        $this->access_token = $token->getAccessToken() ?? $this->access_token;
        $this->refresh_token = $token->getRefreshToken() ?? $this->refresh_token;
        $this->expires_at = $token->getExpiresAt() ?? $this->expires_at;
        $this->save();
    }

    public function stravaClient()
    {
        return $this->belongsTo(StravaClient::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function scopeForUser(Builder $query, int $userId)
    {
        $query->where('user_id', $userId);
    }

    public static function scopeEnabled(Builder $query)
    {
        return $query->where('disabled', false);
    }

    public static function scopeActive(Builder $query)
    {
        $query->where('expires_at', '>', Carbon::now()->addMinutes(2));
    }

    public function expired()
    {
        return $this->expires_at->subMinutes(2)->isPast();
    }

    protected static function newFactory()
    {
        return new StravaTokenFactory();
    }

}
