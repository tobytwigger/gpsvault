<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class File extends Model
{
    use HasFactory;

    protected $fillable = [
        'path', 'filename', 'size', 'title', 'caption', 'mimetype', 'disk', 'extension'
    ];

    protected $appends = [
        'preview_url'
    ];

    public function fullPath()
    {
        return Storage::path($this->path);
    }

    public function getFileContents()
    {
        return Storage::get($this->path);
    }

    public function getPreviewUrlAttribute()
    {
        return Storage::url($this->path);
    }

}
