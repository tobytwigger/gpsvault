<?php

namespace App\Models;

use App\Jobs\AnalyseRouteFile;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Laravel\Scout\Searchable;

/**
 * App\Models\Route
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $description
 * @property string|null $notes
 * @property bool $public
 * @property int|null $file_id
 * @property int $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\File|null $file
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\File[] $files
 * @property-read int|null $files_count
 * @property-read mixed $cover_image
 * @property-read \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Relations\HasMany|object|null $path
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\RoutePath[] $mainPath
 * @property-read int|null $main_path_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Place[] $places
 * @property-read int|null $places_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\RoutePath[] $routePaths
 * @property-read int|null $route_paths_count
 * @property-read \App\Models\Stage|null $stage
 * @property-read \App\Models\User $user
 * @method static \Database\Factories\RouteFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Route newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Route newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Route query()
 * @method static \Illuminate\Database\Eloquent\Builder|Route whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Route whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Route whereFileId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Route whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Route whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Route whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Route wherePublic($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Route whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Route whereUserId($value)
 * @mixin \Eloquent
 */
class Route extends Model
{
    use HasFactory, Searchable;

    protected $fillable = [
        'name', 'description', 'notes', 'file_id', 'public', 'distance', 'elevation',
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
        $routePath = $this->routePaths()->whereNotNull('thumbnail_id')->latest()->first();
        if($routePath) {
            return route('file.preview', $routePath->thumbnail);
        }

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

    public function analyse()
    {
        dispatch(new AnalyseRouteFile($this));
    }

    /**
     * Get the latest route path along with its points.
     *
     * @return Model|\Illuminate\Database\Eloquent\Relations\HasMany|object|null
     */
    public function getPathAttribute()
    {
        return $this->routePaths()->latest()->first()?->append('waypoints');
    }

    /**
     * Get the latest route path as a query.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
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
