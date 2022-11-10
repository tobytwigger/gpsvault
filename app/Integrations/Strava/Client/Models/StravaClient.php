<?php

namespace App\Integrations\Strava\Client\Models;

use App\Integrations\Strava\Client\Authentication\StravaToken;
use App\Models\User;
use Database\Factories\StravaClientFactory;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Linkeys\UrlSigner\Models\Link;
use Linkeys\UrlSigner\Support\LinkRepository\LinkRepository;

/**
 * App\Integrations\Strava\Client\Models\StravaClient
 *
 * @property int $id
 * @property int $user_id
 * @property string|null $name
 * @property string|null $description
 * @property mixed $client_id
 * @property mixed $client_secret
 * @property bool $enabled
 * @property bool $public
 * @property mixed $webhook_verify_token
 * @property string|null $invitation_link_uuid
 * @property int $used_15_min_calls
 * @property int $used_daily_calls
 * @property int $limit_15_min
 * @property int $limit_daily
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read string|null $invitation_link
 * @property-read bool|null $invitation_link_expired
 * @property-read \DateTimeInterface|null $invitation_link_expires_at
 * @property-read bool $is_connected
 * @property-read User $owner
 * @property-read \Illuminate\Database\Eloquent\Collection|User[] $sharedUsers
 * @property-read int|null $shared_users_count
 * @property-read \Illuminate\Database\Eloquent\Collection|StravaToken[] $tokens
 * @property-read int|null $tokens_count
 * @method static Builder|StravaClient connected(int $userId)
 * @method static Builder|StravaClient enabled()
 * @method static Builder|StravaClient excluding(array $excluding)
 * @method static \Database\Factories\StravaClientFactory factory(...$parameters)
 * @method static Builder|StravaClient forUser(int $userId)
 * @method static Builder|StravaClient newModelQuery()
 * @method static Builder|StravaClient newQuery()
 * @method static Builder|StravaClient public()
 * @method static Builder|StravaClient query()
 * @method static Builder|StravaClient whereClientId($value)
 * @method static Builder|StravaClient whereClientSecret($value)
 * @method static Builder|StravaClient whereCreatedAt($value)
 * @method static Builder|StravaClient whereDescription($value)
 * @method static Builder|StravaClient whereEnabled($value)
 * @method static Builder|StravaClient whereId($value)
 * @method static Builder|StravaClient whereInvitationLinkUuid($value)
 * @method static Builder|StravaClient whereLimit15Min($value)
 * @method static Builder|StravaClient whereLimitDaily($value)
 * @method static Builder|StravaClient whereName($value)
 * @method static Builder|StravaClient wherePublic($value)
 * @method static Builder|StravaClient whereUpdatedAt($value)
 * @method static Builder|StravaClient whereUsed15MinCalls($value)
 * @method static Builder|StravaClient whereUsedDailyCalls($value)
 * @method static Builder|StravaClient whereUserId($value)
 * @method static Builder|StravaClient whereWebhookVerifyToken($value)
 * @method static Builder|StravaClient withSpaces()
 * @mixin \Eloquent
 */
class StravaClient extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'client_secret',
        'name',
        'description',
    ];

    protected $hidden = [
        'client_id', 'client_secret',
    ];

    protected $appends = [
        'is_connected',
        'invitation_link',
        'invitation_link_expired',
        'invitation_link_expires_at',
    ];

    protected $casts = [
        'client_id' => 'encrypted',
        'client_secret' => 'encrypted',
        'webhook_verify_token' => 'encrypted',
        'used_15_min_calls' => 'integer',
        'used_daily_calls' => 'integer',
        'limit_15_min' => 'integer',
        'limit_daily' => 'integer',
        'invitation_link_expires_at' => 'datetime',
        'enabled' => 'boolean',
        'public' => 'boolean',
    ];

    protected static function booted()
    {
        static::creating(function (StravaClient $client) {
            if ($client->user_id === null) {
                $client->user_id = Auth::id();
            }
            if ($client->webhook_verify_token === null) {
                $client->webhook_verify_token = Str::random(20);
            }
        });
    }

    public static function scopeExcluding(Builder $query, array $excluding)
    {
        $query->when(
            !empty($excluding),
            fn (Builder $query) => $query->whereNotIn('strava_clients.id', $excluding)
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
        $query->whereHas('tokens', fn (Builder $subquery) => $subquery->forUser($userId)->active()->enabled());
    }

    public static function scopeForUser(Builder $query, int $userId)
    {
        $query->where('user_id', $userId)
            ->orWhereHas('sharedUsers', function (Builder $query) use ($userId) {
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
     * Check if the strava client has space to make a new request.
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
        if (Auth::check()) {
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

    public function getInvitationLinkExpiresAtAttribute(): ?DateTimeInterface
    {
        return $this->getInvitationLink()?->expiry;
    }

    public function getInvitationLinkExpiredAttribute(): ?bool
    {
        return $this->getInvitationLink()?->expired();
    }

    public function getInvitationLink(): ?Link
    {
        if ($this->invitation_link_uuid !== null) {
            return app(LinkRepository::class)->findByUuid($this->invitation_link_uuid);
        }

        return null;
    }

    protected static function newFactory()
    {
        return new StravaClientFactory();
    }
}
