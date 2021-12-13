<?php

namespace App\Models;

use App\Integrations\Dropbox\Models\DropboxToken;
use App\Integrations\Strava\StravaToken;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\App;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'profile_photo_url',
    ];

    protected static function booted()
    {
        static::deleting(function(User $user) {
            $user->deleteProfilePhoto();
            $user->tokens->each->delete();
            $user->stravaTokens()->delete();
            $user->syncs()->delete();
            $user->dropboxTokens()->delete();
            $user->activities()->delete();
            $user->files()->delete();
            $user->connectionLogs()->delete();
        });
    }

    public function connectionLogs()
    {
        return $this->hasMany(ConnectionLog::class);
    }

    public function stravaTokens()
    {
        return $this->hasMany(StravaToken::class);
    }

    public function dropboxTokens()
    {
        return $this->hasMany(DropboxToken::class);
    }

    public function disk()
    {
        return App::isLocal() ? 'local' : 's3';
    }

    public function syncs()
    {
        return $this->hasMany(Sync::class);
    }

    public function activities()
    {
        return $this->hasMany(Activity::class);
    }

    public function files()
    {
        return $this->hasMany(File::class);
    }

}
