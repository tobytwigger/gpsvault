<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use MStaack\LaravelPostgis\Eloquent\PostgisTrait;

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

    public function routes()
    {
        return $this->belongsToMany(Route::class)
            ->using(PlaceRoute::class);
    }
}
