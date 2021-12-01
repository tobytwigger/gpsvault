<?php

namespace App\Integrations\Strava\Import\Models;

use Illuminate\Database\Eloquent\Model;

class ImportResult extends Model
{

    protected $table = 'import_results';

    protected $fillable = [
        'type', 'message', 'success', 'import_id'
    ];

    protected $casts = [
        'success' => 'boolean'
    ];

    public function import()
    {
        return $this->belongsTo(Import::class);
    }

    public static function saveResult(Import $import, string $type, string $message, bool $success): ImportResult
    {
        return ImportResult::create([
            'type' => $type,
            'message' => $message,
            'success' => $success,
            'import_id' => $import->id
        ]);
    }

}
