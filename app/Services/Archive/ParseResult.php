<?php

namespace App\Services\Archive;

class ParseResult
{

    private array $metadata;

    private array $files;

    public function __construct(array $metadata, array $files)
    {
        $this->metadata = $metadata;
        $this->files = $files;
    }

    /**
     * @return array
     */
    public function getMetadata(): array
    {
        return $this->metadata;
    }

    /**
     * @return array
     */
    public function getFiles(): array
    {
        return $this->files;
    }

}
