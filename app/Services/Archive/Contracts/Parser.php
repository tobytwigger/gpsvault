<?php

namespace App\Services\Archive\Contracts;

use App\Services\Archive\ParseResults;
use App\Services\Archive\ParseResult;

interface Parser
{

    public function parse($item): ParseResult;

    public function canHandle($item): bool;

}
