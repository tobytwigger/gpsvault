<?php

namespace App\Services\Archive\Parser\Parsers;

use App\Models\User;
use App\Services\Archive\Contracts\Parser;
use App\Services\Archive\Parser\FileResource;
use App\Services\Archive\ParseResult;
use App\Services\Archive\Traits\CreatesParseResult;

class UserParser implements Parser
{
    use CreatesParseResult;

    public function canHandle($item): bool
    {
        return $item instanceof User;
    }

    /**
     * @param User $item
     * @return FileResource[]
     */
    public function parse($item): ParseResult
    {
        $this->addMetaData('user', $item->toArray());

        return $this->result();
    }
}
