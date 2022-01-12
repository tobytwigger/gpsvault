<?php

namespace App\Services\Analysis\Parser;

use App\Models\File;
use App\Services\Analysis\Analyser\Analysis;
use App\Services\Analysis\Parser\Parsers\ParserContract;

interface ParserFactoryContract
{

    public function parse(File $file): Analysis;

    public function parser(string $type): ParserContract;

    public function registerCustomParser(string $type, \Closure $creator);


}
