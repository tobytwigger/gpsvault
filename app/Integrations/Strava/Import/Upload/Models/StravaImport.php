<?php

namespace App\Integrations\Strava\Import\Upload\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

/**
 * App\Integrations\Strava\Import\Upload\Models\StravaImport.
 *
 * @property int $id
 * @property int $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Integrations\Strava\Import\Upload\Models\StravaImportResult[] $stravaImportResults
 * @property-read int|null $strava_import_results_count
 * @method static \Illuminate\Database\Eloquent\Builder|StravaImport newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StravaImport newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StravaImport query()
 * @method static \Illuminate\Database\Eloquent\Builder|StravaImport whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StravaImport whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StravaImport whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StravaImport whereUserId($value)
 * @mixin \Eloquent
 */
class StravaImport extends Model
{
    protected $fillable = ['user_id'];

    protected $casts = [];

    protected $with = [
        'stravaImportResults',
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
