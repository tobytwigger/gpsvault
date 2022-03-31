<?php

namespace App\Models;

use App\Traits\HasStats;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Laravel\Scout\Searchable;

class Route extends Model
{
    use HasFactory, HasStats, Searchable;

    protected $fillable = [
        'name', 'description', 'notes', 'file_id', 'public'
    ];

    protected $appends = [
        'cover_image', 'distance'
    ];

    protected $casts = [
        'public' => 'boolean'
    ];

    public function toSearchableArray()
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
            'notes' => $this->notes,
            'user_id' => $this->user_id
        ];
    }

    public function getCoverImageAttribute()
    {
        $image = $this->files()->where('mimetype', 'LIKE', 'image/%')->first();
        if($image) {
            return route('file.preview', $image);
        }
        return null;
    }

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
