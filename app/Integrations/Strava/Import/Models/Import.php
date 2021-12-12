<?php

namespace App\Integrations\Strava\Import\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Import extends Model
{

    protected $table = 'imports';

    protected $fillable = ['user_id'];

    protected $casts = [];

    protected $with = [
        'importResults'
    ];

    protected static function booted()
    {
        static::creating(function(Import $import) {
            if($import->user_id === null && Auth::check()) {
                $import->user_id = Auth::id();
            }
        });
    }

    public function importResults()
    {
        return $this->hasMany(ImportResult::class);
    }

}
