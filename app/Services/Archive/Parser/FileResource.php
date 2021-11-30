<?php

namespace App\Services\Archive\Parser;

use App\Models\File;

class FileResource
{

    private string $newDirectory;

    private string $newName;

    private File $file;

    public function __construct(File $file)
    {
        $this->file = $file;
    }

    public function getFile(): File
    {
        return $this->file;
    }

    public static function new(File $file): FileResource
    {
        return new static($file);
    }

    public function id()
    {
        return $this->file->id;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->file->path;
    }

    public function fullPath(): string
    {
        return $this->file->fullPath();
    }

    /**
     * @return string|null
     */
    public function getNewDirectory(): string
    {
        // TODO Get the archive path from the file type
        return $this->newDirectory ?? dirname($this->getPath());
    }

    /**
     * @param string|null $newDirectory
     * @return FileResource
     */
    public function setNewDirectory(string $newDirectory): FileResource
    {
        $this->newDirectory = $newDirectory;
        return $this;
    }

    /**
     * @return string
     */
    public function getNewName(): string
    {
        return $this->newName ?? basename($this->getPath());
    }

    /**
     * @param string $newName
     * @return FileResource
     */
    public function setNewName(string $newName): FileResource
    {
        $this->newName = $newName;
        return $this;
    }

    public function getNewPath(): string
    {
        return sprintf('%s/%s', $this->getNewDirectory(), $this->getNewName());
    }

}
