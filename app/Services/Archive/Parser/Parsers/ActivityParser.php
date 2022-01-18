<?php

namespace App\Services\Archive\Parser\Parsers;

use App\Models\Activity;
use App\Models\File;
use App\Services\Archive\ParseResult;
use App\Services\Archive\ParseResults;
use App\Services\Archive\Parser\FileResource;
use App\Services\Archive\Contracts\Parser;
use App\Services\Archive\Traits\CreatesParseResult;

class ActivityParser implements Parser
{
    use CreatesParseResult;

    public function canHandle($item): bool
    {
        return $item instanceof Activity;
    }

    /**
     * @param Activity $item
     * @return FileResource[]
     */
    public function parse($item): ParseResult
    {
        $this->addMetaData('activity', $item->toArray());
        $file = $item->file()->first();
        if($file) {
            $this->addFile($this->parseActivityFile($item->id, $file));
        }
        foreach($item->files()->get() as $file) {
            $this->addFile($this->parseActivityMediaFile($item->id, $file));
        }
        return $this->result();
    }

    private function parseActivityFile(int $activityId, File $file): FileResource
    {
        return FileResource::new($file)
            ->setNewName(sprintf('Activity_%u_Activity_File_%u.%s', $activityId, $file->id, $file->extension));
    }

    private function parseActivityMediaFile(int $activityId, File $mediaFile): FileResource
    {
        return FileResource::new($mediaFile)
            ->setNewName(sprintf('Activity_%u_Media_File_%u.%s', $activityId, $mediaFile->id, $mediaFile->extension));
    }
}
