<?php

namespace App\Integrations\Strava\Import\Models;

use Illuminate\Database\Eloquent\Model;

class Import extends Model
{

    protected $table = 'imports';

    protected $fillable = [];

    protected $casts = [];

    protected $with = [
        'importResults'
    ];

    public function importResults()
    {
        return $this->hasMany(ImportResult::class);
    }

}
