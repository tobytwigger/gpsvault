<?php

namespace App\Models;

use App\Jobs\AnalyseActivityFile;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Route extends Model
{
    use HasFactory;

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

}
