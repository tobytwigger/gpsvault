<?php

namespace App\Services\Archive;

class ParseResults
{

    private array $files = [];

    private array $metadata = [];

    public function mergeResults(ParseResult $result)
    {
        foreach($result->getMetadata() as $file => $data) {
            if(!array_key_exists($file, $this->metadata)) {
                $this->metadata[$file] = [];
            }
            $this->metadata[$file][] = $data;
        }
        $this->files = array_merge($this->files, $result->getFiles());
    }

    /**
     * @return array
     */
    public function getFiles(): array
    {
        return $this->files;
    }

    /**
     * @return array
     */
    public function getAllMetadata(): array
    {
        return $this->metadata;
    }

}
