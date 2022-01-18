<?php

namespace App\Models;

use App\Traits\HasStats;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Route extends Model
{
    use HasFactory, HasStats;

    protected $fillable = [
        'name', 'description', 'notes', 'file_id', 'stats_type', 'stats_id'
    ];

    protected static function booted()
    {
        static::creating(function(Route $route) {
            if($route->user_id === null) {
                $route->user_id = Auth::id();
            }
        });
        static::deleting(function(Route $route) {
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

}
