<?php

namespace App\Models;

use App\Integrations\Strava\UsesStrava;
use App\Services\Dashboard\DefaultDashboardFactory;
use App\Traits\HasAdditionalData;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\App;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, HasProfilePhoto, Notifiable, TwoFactorAuthenticatable, HasAdditionalData, UsesStrava, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
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
        static::created(function(User $user) {
            app(DefaultDashboardFactory::class)->syncDashboards($user);
        });

        static::deleting(function (User $user) {
            $user->deleteProfilePhoto();
            $user->tokens->each->delete();
//            $user->dropboxTokens()->delete();
            $user->activities()->delete();
            $user->files()->delete();
        });
    }

//    public function dropboxTokens()
//    {
//        return $this->hasMany(DropboxToken::class);
//    }

    public function disk()
    {
        if (App::isLocal()) {
            return 'local';
        }
        if (App::environment('testing')) {
            return 'test-fake';
        }

        return 's3';
    }

    public function activities()
    {
        return $this->hasMany(Activity::class);
    }

    public function routes()
    {
        return $this->hasMany(Route::class);
    }

    public function files()
    {
        return $this->hasMany(File::class);
    }
}
