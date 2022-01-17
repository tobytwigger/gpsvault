<?php

namespace App\Models;

use App\Jobs\AnalyseRouteFile;
use App\Traits\HasStats;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;

class Route extends Model
{
    use HasFactory, HasStats;

    protected $appends = [
        'stats'
    ];

    protected $fillable = [
        'name', 'description', 'notes', 'route_file_id'
    ];

    protected static function booted()
    {
        static::creating(function(Route $route) {
            if($route->user_id === null) {
                $route->user_id = Auth::id();
            }
        });
        static::deleting(function(Route $route) {
            $route->routeFile()->delete();
            $route->files()->delete();
            $route->statRelationship()->delete();
        });

        static::saved(function(Route $route) {
            if ($route->isDirty('route_file_id') && $route->hasRouteFile()) {
                $route->refresh();
                AnalyseRouteFile::dispatch($route);
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function routeFile()
    {
        return $this->belongsTo(File::class, 'route_file_id');
    }

    public function files()
    {
        return $this->belongsToMany(File::class);
    }

    public function hasRouteFile(): bool
    {
        return $this->routeFile()->exists();
    }

    public function statRelationship(): HasMany
    {
        return $this->hasMany(RouteStats::class);
    }
}
