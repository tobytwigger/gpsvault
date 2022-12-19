<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use MStaack\LaravelPostgis\Eloquent\PostgisTrait;

/**
 * App\Models\Place
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property string $type
 * @property string|null $url
 * @property string|null $phone_number
 * @property string|null $email
 * @property string|null $address
 * @property int|null $user_id
 * @property mixed $location
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Route[] $routes
 * @property-read int|null $routes_count
 * @property-read \App\Models\User|null $user
 * @method static \Database\Factories\PlaceFactory factory(...$parameters)
 * @method static \MStaack\LaravelPostgis\Eloquent\Builder|Place newModelQuery()
 * @method static \MStaack\LaravelPostgis\Eloquent\Builder|Place newQuery()
 * @method static \MStaack\LaravelPostgis\Eloquent\Builder|Place query()
 * @method static \MStaack\LaravelPostgis\Eloquent\Builder|Place whereAddress($value)
 * @method static \MStaack\LaravelPostgis\Eloquent\Builder|Place whereCreatedAt($value)
 * @method static \MStaack\LaravelPostgis\Eloquent\Builder|Place whereDescription($value)
 * @method static \MStaack\LaravelPostgis\Eloquent\Builder|Place whereEmail($value)
 * @method static \MStaack\LaravelPostgis\Eloquent\Builder|Place whereId($value)
 * @method static \MStaack\LaravelPostgis\Eloquent\Builder|Place whereLocation($value)
 * @method static \MStaack\LaravelPostgis\Eloquent\Builder|Place whereName($value)
 * @method static \MStaack\LaravelPostgis\Eloquent\Builder|Place wherePhoneNumber($value)
 * @method static \MStaack\LaravelPostgis\Eloquent\Builder|Place whereType($value)
 * @method static \MStaack\LaravelPostgis\Eloquent\Builder|Place whereUpdatedAt($value)
 * @method static \MStaack\LaravelPostgis\Eloquent\Builder|Place whereUrl($value)
 * @method static \MStaack\LaravelPostgis\Eloquent\Builder|Place whereUserId($value)
 * @mixin \Eloquent
 */
class Place extends Model
{
    use HasFactory, PostgisTrait;

    protected $fillable = [
        'name', 'description', 'type', 'url', 'phone_number', 'email', 'address', 'location',
    ];

    protected $postgisFields = [
        'location',
    ];

    protected $postgisTypes = [
        'location' => [
            'geomtype' => 'geography',
            'srid' => 4326,
        ],
    ];

    protected static function booted()
    {
        static::creating(function (Place $place) {
            if ($place->user_id === null) {
                $place->user_id = Auth::id();
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
