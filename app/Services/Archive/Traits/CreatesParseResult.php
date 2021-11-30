<?php

namespace App\Services\Archive\Traits;

use App\Services\Archive\Parser\FileResource;
use App\Services\Archive\ParseResult;

trait CreatesParseResult
{

    private array $metaData = [];

    private array $files = [];

    public function addMetaData(string $file, array $metadata): static
    {
        if(!isset($this->metaData[$file])) {
            $this->metaData[$file] = [];
        }
        $this->metaData[$file][] = $metadata;
        return $this;
    }

    public function addFile(FileResource $file): static
    {
        $this->addMetaData('files', $file->getFile()->toArray());
        $this->files[] = $file;
        return $this;
    }

    public function result(): ParseResult
    {
        return new ParseResult($this->metaData, $this->files);
    }

}
