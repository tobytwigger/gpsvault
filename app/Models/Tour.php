<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Tour extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'description', 'notes', 'marked_as_started_at', 'marked_as_finished_at'
    ];

    protected $casts = [
        'marked_as_started_at' => 'datetime',
        'marked_as_finished_at' => 'datetime'
    ];

    protected static function booted()
    {
        static::creating(function(Tour $tour) {
            if($tour->user_id === null) {
                $tour->user_id = Auth::id();
            }
        });
    }

}
