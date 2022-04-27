<?php

namespace App\Services\Analysis\Parser\Parsers;

use App\Models\File;
use App\Services\Analysis\Analyser\Analysis;

interface ParserContract
{
    public function read(File $file): Analysis;
}
