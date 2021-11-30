<?php

namespace App\Services\Archive\Parser\Parsers;

use App\Models\ConnectionLog;
use App\Services\Archive\ParseResult;
use App\Services\Archive\Parser\FileResource;
use App\Services\Archive\Contracts\Parser;
use App\Services\Archive\Traits\CreatesParseResult;

class ConnectionLogParser implements Parser
{
    use CreatesParseResult;

    public function canHandle($item): bool
    {
        return $item instanceof ConnectionLog;
    }

    /**
     * @param ConnectionLog $item
     * @return FileResource[]
     */
    public function parse($item): ParseResult
    {
        $this->addMetaData('connection_logs', $item->toArray());
        return $this->result();
    }

}
