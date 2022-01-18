<?php

namespace App\Services\Archive\Parser\Parsers;

use App\Services\Sync\Sync;
use App\Models\File;
use App\Services\Archive\ParseResult;
use App\Services\Archive\Parser\FileResource;
use App\Services\Archive\Contracts\Parser;
use App\Services\Archive\Traits\CreatesParseResult;

class SyncParser implements Parser
{
    use CreatesParseResult;

    public function canHandle($item): bool
    {
        return $item instanceof Sync;
    }

    /**
     * @param Sync $item
     * @return FileResource[]
     */
    public function parse($item): ParseResult
    {
        $this->addMetaData('sync', $item->toArray());
        return $this->result();
    }

}
