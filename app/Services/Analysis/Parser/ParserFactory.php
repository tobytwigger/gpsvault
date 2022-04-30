<?php

namespace App\Services\Analysis\Parser;

use App\Models\File;
use App\Services\Analysis\Analyser\Analysis;
use App\Services\Analysis\Parser\Parsers\FitParser;
use App\Services\Analysis\Parser\Parsers\GpxParser;
use App\Services\Analysis\Parser\Parsers\ParserContract;
use App\Services\Analysis\Parser\Parsers\TcxParser;
use Closure;
use Exception;

class ParserFactory implements ParserFactoryContract
{
    private array $custom = [];

    public function parse(File $file): Analysis
    {
        return $this->parser($file->extension)->read($file);
    }

    public function parser(string $type): ParserContract
    {
        if (array_key_exists($type, $this->custom)) {
            return call_user_func($this->custom[$type]);
        }

        $method = sprintf('create%sParser', ucfirst($type));
        if (method_exists($this, $method)) {
            return $this->{$method}();
        }

        throw new Exception(sprintf('Cannot parse files of type [%s]', $type));
    }

    public function registerCustomParser(string $type, Closure $creator)
    {
        $this->custom[$type] = $creator;
    }

    public function createGpxParser()
    {
        return new GpxParser();
    }

    public function createFitParser()
    {
        return new FitParser();
    }

    public function createTcxParser()
    {
        return new TcxParser();
    }
}
