<?php

namespace App\Services\Archive\Parser\Parsers;

use App\Models\Tour;
use App\Services\Archive\Contracts\Parser;
use App\Services\Archive\Parser\FileResource;
use App\Services\Archive\ParseResult;
use App\Services\Archive\Traits\CreatesParseResult;

class TourParser implements Parser
{
    use CreatesParseResult;

    public function canHandle($item): bool
    {
        return $item instanceof Tour;
    }

    /**
     * @param Tour $item
     * @return FileResource[]
     */
    public function parse($item): ParseResult
    {
        $this->addMetaData('tour', $item->load('stages')->toArray());

        return $this->result();
    }
}
