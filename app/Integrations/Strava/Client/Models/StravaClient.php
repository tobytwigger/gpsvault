<?php

namespace App\Integrations\Strava\Client\Models;

use App\Integrations\Strava\Client\Authentication\StravaToken;
use App\Models\User;
use Database\Factories\StravaClientFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Linkeys\UrlSigner\Models\Link;
use Linkeys\UrlSigner\Support\LinkRepository\LinkRepository;

class StravaClient extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'client_secret',
        'name',
        'description'
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
        'limit_15_min' => 'integer',
        'limit_daily' => 'integer',
        'pending_calls' => 'integer',
        'invitation_link_expires_at' => 'datetime',
        'enabled' => 'boolean',
        'public' => 'boolean'
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

    public static function scopeExcluding(Builder $query, array $excluding)
    {
        $query->when(!empty($excluding),
            fn(Builder $query) => $query->whereNotIn('strava_clients.id', $excluding)
        );
    }

    public static function scopeEnabled(Builder $query)
    {
        $query->where('enabled', true);
    }

    public function tokens()
    {
        return $this->hasMany(StravaToken::class);
    }

    public static function scopeConnected(Builder $query, int $userId)
    {
        $query->whereHas('tokens', fn(Builder $subquery) => $subquery->forUser($userId)->active()->enabled());
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
        return $this->used_15_min_calls < $this->limit_15_min && $this->used_daily_calls < $this->limit_daily;
    }

    public static function scopeWithSpaces(Builder $query)
    {
        $query->whereColumn('used_15_min_calls', '<', 'limit_15_min')
            ->whereColumn('used_daily_calls', '<', 'limit_daily');
    }

    public static function scopePublic(Builder $query)
    {
        $query->where('public', true);
    }

    public function getIsConnectedAttribute(): bool
    {
        if(Auth::check()) {
            return $this->isConnected(Auth::id());
        }
        return false;
    }

    public function isConnected(int $userId): bool
    {
        return $this->tokens()->enabled()->active()->forUser($userId)->count() > 0;
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

    protected static function newFactory()
    {
        return new StravaClientFactory();
    }
}
