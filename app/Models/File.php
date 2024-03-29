<?php

namespace App\Models;

use App\Jobs\CreateThumbnailImage;
use App\Services\File\FileUploader;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * App\Models\File.
 *
 * @property int $id
 * @property string $path
 * @property string $filename
 * @property string $extension
 * @property string $type
 * @property int $size
 * @property string|null $title
 * @property string|null $caption
 * @property string $mimetype
 * @property string $disk
 * @property string $hash
 * @property int $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Database\Factories\FileFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|File newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|File newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|File query()
 * @method static \Illuminate\Database\Eloquent\Builder|File whereCaption($value)
 * @method static \Illuminate\Database\Eloquent\Builder|File whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|File whereDisk($value)
 * @method static \Illuminate\Database\Eloquent\Builder|File whereExtension($value)
 * @method static \Illuminate\Database\Eloquent\Builder|File whereFilename($value)
 * @method static \Illuminate\Database\Eloquent\Builder|File whereHash($value)
 * @method static \Illuminate\Database\Eloquent\Builder|File whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|File whereMimetype($value)
 * @method static \Illuminate\Database\Eloquent\Builder|File wherePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|File whereSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder|File whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|File whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|File whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|File whereUserId($value)
 * @mixin \Eloquent
 * @property int|null $thumbnail_id
 * @property bool $is_thumbnail
 * @property-read File|null $thumbnail
 * @method static \Illuminate\Database\Eloquent\Builder|File whereIsThumbnail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|File whereThumbnailId($value)
 */
class File extends Model
{
    use HasFactory;

    protected $fillable = [
        'path', 'filename', 'size', 'title', 'caption', 'mimetype', 'disk', 'extension', 'disk', 'user_id', 'type', 'hash', 'thumbnail_id',
    ];

    protected $casts = [
        'user_id' => 'integer',
    ];

    protected static function booted()
    {
        static::creating(function (File $file) {
            if ($file->user_id === null) {
                $file->user_id = Auth::id();
            }
        });
        static::creating(function (File $file) {
            if ($file->hash === null) {
                $file->hash = md5($file->getFileContents());
            }
        });
        static::created(function (File $file) {
            if ($file->thumbnail_id === null && Str::contains($file->mimetype, 'image') && $file->type !== FileUploader::IMAGE_THUMBNAIL) {
                CreateThumbnailImage::dispatch($file);
            }
        });
        static::deleting(function (File $file) {
            Storage::disk($file->disk)->delete($file->path);
        });
        static::deleted(function (File $file) {
            if ($file->thumbnail_id !== null) {
                $file->thumbnail->delete();
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function fullPath()
    {
        return Storage::disk($this->disk)->path($this->path);
    }

    public function thumbnail()
    {
        return $this->belongsTo(File::class, 'thumbnail_id');
    }

    public function getFileContents()
    {
        return Storage::disk($this->disk)->get($this->path);
    }

    public function returnDownloadResponse(): StreamedResponse
    {
        return Storage::disk($this->disk)->download($this->path, $this->filename);
    }
}
