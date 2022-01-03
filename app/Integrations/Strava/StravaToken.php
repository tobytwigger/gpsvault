<?php

namespace App\Integrations\Strava;

use App\Integrations\Strava\Models\StravaClient;
use App\Models\User;
use Carbon\Carbon;
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

    protected $fillable = [];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::addGlobalScope('enabled', function (Builder $builder) {
            $builder->where('disabled', false);
        });
        static::addGlobalScope('not-expired', function (Builder $builder) {
            $builder->whereRaw("expires_at > STR_TO_DATE(?, '%Y-%m-%d %H:%i:%s')" , Carbon::now()->addMinutes(2)->format('Y-m-d H:i:s'))
                ->orWhereNotNull('refresh_token');
        });
    }

    public function stravaClient()
    {
        return $this->belongsTo(StravaClient::class);
    }

    public static function makeFromStravaToken(\App\Integrations\Strava\Client\Authentication\StravaToken $token, int $clientId)
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

    public function updateFromStravaToken(\App\Integrations\Strava\Client\Authentication\StravaToken $token)
    {
        $this->access_token = $token->getAccessToken() ?? $this->access_token;
        $this->refresh_token = $token->getRefreshToken() ?? $this->refresh_token;
        $this->expires_at = $token->getExpiresAt() ?? $this->expires_at;
        $this->save();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function scopeForUser(Builder $query, int $userId)
    {
        $query->where('user_id', $userId);
    }

    public function expired()
    {
        return $this->expires_at->subMinutes(2)->isPast();
    }

}
