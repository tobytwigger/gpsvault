<?php

namespace App\Integrations\Strava\Import\Upload\Models;

use Illuminate\Database\Eloquent\Model;

class StravaImportResult extends Model
{
    protected $fillable = [
        'type', 'message', 'success', 'strava_import_id', 'data'
    ];

    protected $casts = [
        'success' => 'boolean',
        'data' => 'array'
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
            'data' => $data
        ]);
    }
}
