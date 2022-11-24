<?php

namespace App\Services\Filepond;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Sopamo\LaravelFilepond\Filepond;

class FilePondFile implements Arrayable, Jsonable, \Stringable
{
    private string $filename;

    private int $size;

    private string $mimetype;

    private string $serverId;

    private string $extension;

    /**
     * @param string $filename
     * @param int $size
     * @param string $mimetype
     * @param string $serverId
     * @param string $extension
     */
    public function __construct(string $filename, int $size, string $mimetype, string $serverId, string $extension)
    {
        $this->filename = $filename;
        $this->size = $size;
        $this->mimetype = $mimetype;
        $this->serverId = $serverId;
        $this->extension = $extension;
    }

    public static function fromArray(array $array): static
    {
        return new static(
            $array['filename'],
            $array['size'],
            $array['mimetype'],
            $array['serverId'],
            $array['extension']
        );
    }

    /**
     * @return string
     */
    public function getFilename(): string
    {
        return $this->filename;
    }

    /**
     * @return int
     */
    public function getSize(): int
    {
        return $this->size;
    }

    /**
     * @return string
     */
    public function getMimetype(): string
    {
        return $this->mimetype;
    }

    /**
     * @return string
     */
    public function getServerId(): string
    {
        return $this->serverId;
    }

    /**
     * @return string
     */
    public function getExtension(): string
    {
        return $this->extension;
    }

    public function getPath()
    {
        return app(Filepond::class)->getPathFromServerId($this->serverId);
    }

    /**
     * @return resource|null
     */
    public function readAsStream()
    {
        return Storage::disk(config('filepond.temporary_files_disk'))->readStream($this->getPath());
    }

    public function tempFileNameWithoutPath(): string
    {
        return Str::replace('filepond', '', $this->getPath());
    }

    /**
     * @param string $path Path not including the file name.
     * @param string $disk The disk to use
     * @return string The short path of the file
     */
    public function store(string $path, string $disk): string
    {
        $newPath = $path . (Str::endsWith($path, '/') ? '' : '/') . $this->tempFileNameWithoutPath();

        Storage::disk($disk)->writeStream(
            $newPath,
            $this->readAsStream(),
        );

        return $newPath;
    }

    public function toArray()
    {
        return [
            'filename' => $this->getFilename(),
            'size' => $this->getSize(),
            'mimetype' => $this->getMimetype(),
            'serverId' => $this->getServerId(),
            'extension' => $this->getExtension(),
        ];
    }

    public function toJson($options = 0)
    {
        return json_encode($this->toArray());
    }

    public function __toString()
    {
        return $this->toJson();
    }
}
