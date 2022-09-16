<?php

namespace App\Integrations\Strava\Import\Upload\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class StravaImport extends Model
{
    protected $fillable = ['user_id'];

    protected $casts = [];

    protected $with = [
        'stravaImportResults'
    ];

    protected static function booted()
    {
        static::creating(function (StravaImport $import) {
            if ($import->user_id === null && Auth::check()) {
                $import->user_id = Auth::id();
            }
        });
    }

    public function stravaImportResults()
    {
        return $this->hasMany(StravaImportResult::class);
    }
}
