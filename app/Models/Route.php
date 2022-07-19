<?php

namespace App\Models;

use App\Traits\HasStats;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Laravel\Scout\Searchable;
use MStaack\LaravelPostgis\Eloquent\PostgisTrait;

class Route extends Model
{
    use HasFactory, HasStats, Searchable, PostgisTrait;

    protected $fillable = [
        'name', 'description', 'notes', 'file_id', 'public', 'distance', 'elevation', 'linestring',
    ];

    protected $postgisFields = [
        'linestring',
    ];

    protected $postgisTypes = [
        'linestring' => [
            'geomtype' => 'geography',
            'srid' => 4326,
        ],
    ];

    protected $appends = [
        'cover_image', 'path',
    ];

    protected $casts = [
        'public' => 'boolean',
    ];

    public function toSearchableArray()
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
            'notes' => $this->notes,
            'user_id' => $this->user_id,
        ];
    }

    public function getCoverImageAttribute()
    {
        $image = $this->files()->where('mimetype', 'LIKE', 'image/%')->first();
        if ($image) {
            return route('file.preview', $image);
        }

        return null;
    }

    protected static function booted()
    {
        static::creating(function (Route $route) {
            if ($route->user_id === null) {
                $route->user_id = Auth::id();
            }
        });
        static::deleting(function (Route $route) {
            $route->files()->delete();
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function files()
    {
        return $this->belongsToMany(File::class);
    }

    public function file()
    {
        return $this->belongsTo(File::class);
    }

    public function places()
    {
        return $this->belongsToMany(Place::class)
            ->using(PlaceRoute::class);
    }

    public function getPathAttribute()
    {
        return $this->routePaths()->latest()->with('routePoints')->first();
    }

    public function mainPath()
    {
        return $this->routePaths()->latest()->limit(1);
    }

    public function routePaths()
    {
        return $this->hasMany(RoutePath::class);
    }

    public function stage()
    {
        return $this->hasOne(Stage::class);
    }
}
