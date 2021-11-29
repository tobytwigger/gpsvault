<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class File extends Model
{
    use HasFactory;

    protected $fillable = [
        'path', 'filename', 'size', 'title', 'caption', 'mimetype', 'disk', 'extension', 'disk', 'user_id'
    ];

    protected $casts = [
        'user_id' => 'integer'
    ];

    protected $appends = [
        'preview_url'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected static function booted()
    {
        static::creating(function(File $file) {
            if($file->user_id === null) {
                $file->user_id = Auth::id();
            }
        });
        static::deleted(function(File $file) {
            Storage::disk($file->disk)->delete($file->path);
        });
    }

    public function fullPath()
    {
        return Storage::disk($this->disk)->path($this->path);
    }

    public function getFileContents()
    {
        return Storage::disk($this->disk)->get($this->path);
    }

    public function getPreviewUrlAttribute()
    {
        return Storage::disk($this->disk)->url($this->path);
    }

}
