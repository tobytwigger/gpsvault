<?php

namespace App\Integrations\Strava\Import\Upload\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Integrations\Strava\Import\Upload\Models\StravaImportResult
 *
 * @property int $id
 * @property string $type
 * @property string $message
 * @property bool $success
 * @property array|null $data
 * @property int $strava_import_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Integrations\Strava\Import\Upload\Models\StravaImport $stravaImport
 * @method static \Illuminate\Database\Eloquent\Builder|StravaImportResult newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StravaImportResult newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StravaImportResult query()
 * @method static \Illuminate\Database\Eloquent\Builder|StravaImportResult whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StravaImportResult whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StravaImportResult whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StravaImportResult whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StravaImportResult whereStravaImportId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StravaImportResult whereSuccess($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StravaImportResult whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StravaImportResult whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class StravaImportResult extends Model
{
    protected $fillable = [
        'type', 'message', 'success', 'strava_import_id', 'data',
    ];

    protected $casts = [
        'success' => 'boolean',
        'data' => 'array',
    ];

    public function stravaImport()
    {
        return $this->belongsTo(StravaImport::class);
    }

    public static function saveResult(StravaImport $import, string $type, string $message, bool $success, array $data): StravaImportResult
    {
        return StravaImportResult::create([
            'type' => $type,
            'message' => $message,
            'success' => $success,
            'strava_import_id' => $import->id,
            'data' => $data,
        ]);
    }
}
