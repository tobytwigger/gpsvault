<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'place_id', 'stage_id', 'notes', 'price'
    ];

    protected $casts = [
        'price' => 'float'
    ];

    public function stage()
    {
        return $this->belongsTo(Stage::class);
    }

    public function place()
    {
        return $this->belongsTo(Place::class);
    }

    public static function scopeAccommodation(Builder $query)
    {
        $query->where('type', 'accommodation');
    }
}
