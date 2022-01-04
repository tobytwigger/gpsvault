<?php

namespace App\Integrations\Strava\Models;

use App\Integrations\Strava\StravaToken;
use App\Models\ConnectionLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Linkeys\UrlSigner\Models\Link;
use Linkeys\UrlSigner\Support\LinkRepository\LinkRepository;

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
        'is_connected',
        'invitation_link',
        'invitation_link_expired',
        'invitation_link_expires_at'
    ];

    protected $casts = [
        'client_id' => 'encrypted',
        'client_secret' => 'encrypted',
        'webhook_verify_token' => 'encrypted',
        'used_15_min_calls' => 'integer',
        'used_daily_calls' => 'integer',
        'pending_calls' => 'integer',
        'invitation_link_expires_at' => 'datetime',
        'enabled' => 'boolean'
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

    public function getInvitationLinkAttribute(): ?string
    {
        return $this->getInvitationLink()?->getFullUrl();
    }

    public function getInvitationLinkExpiresAtAttribute(): ?\DateTimeInterface
    {
        return $this->getInvitationLink()?->expiry;
    }

    public function getInvitationLinkExpiredAttribute(): ?bool
    {
        return $this->getInvitationLink()?->expired();
    }

    public function getInvitationLink(): ?Link
    {
        if($this->invitation_link_uuid !== null) {
            return app(LinkRepository::class)->findByUuid($this->invitation_link_uuid);
        }
        return null;
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

    public static function scopeEnabled(Builder $query)
    {
        $query->where('enabled', true);
    }

    public function connectionLogs(): Collection
    {
        return ConnectionLog::whereAdditionalData('strava_client_id', $this->id)->get();
    }

    public function redirectUrl(): string
    {
        return route('strava.callback', ['client' => $this->id]);
    }

    public static function scopeForUser(Builder $query, int $userId)
    {
        $query->where('user_id', $userId)
            ->orWhereHas('sharedUsers', function(Builder $query) use ($userId) {
                $query->where('users.id', $userId);
            })
            ->orWhere('public', true);
    }

    public function sharedUsers()
    {
        return $this->belongsToMany(User::class);
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Check if the strava client has space to make a new request
     * @return bool
     */
    public function hasSpaces(): bool
    {
        return $this->used_15_min_calls < 100 && $this->used_daily_calls < 1000;
    }

    public static function scopeWithSpaces(Builder $query)
    {
        $query->where('used_15_min_calls', '<', 100)
            ->where('used_daily_calls', '<', 1000);
    }

    public function reserveSpace()
    {
        $this->pending_calls = $this->pending_calls + 1;
        $this->save();
    }
}
