<?php

namespace App\Integrations\Strava\Import\Models;

use Illuminate\Database\Eloquent\Model;

class ImportResult extends Model
{
    protected $table = 'import_results';

    protected $fillable = [
        'type', 'message', 'success', 'import_id', 'data'
    ];

    protected $casts = [
        'success' => 'boolean',
        'data' => 'array'
    ];

    public function import()
    {
        return $this->belongsTo(Import::class);
    }

    public static function saveResult(Import $import, string $type, string $message, bool $success, array $data): ImportResult
    {
        return ImportResult::create([
            'type' => $type,
            'message' => $message,
            'success' => $success,
            'import_id' => $import->id,
            'data' => $data
        ]);
    }
}
