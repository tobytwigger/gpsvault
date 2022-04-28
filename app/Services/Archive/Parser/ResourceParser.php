<?php

namespace App\Services\Archive\Parser;

use App\Services\Archive\Contracts\Parser;
use App\Services\Archive\ParseResult;

class ResourceParser
{

    /**
     * @var array|Parser[]
     */
    private static array $parsers = [];

    public static function withParser(string $parser)
    {
        if (!is_a($parser, Parser::class, true)) {
            throw new \Exception(sprintf('Parser [%s] must extend the Parser contract', $parser));
        }
        static::$parsers[] = app($parser);
    }

    public function parse($item): ParseResult
    {
        foreach (static::$parsers as $parser) {
            if ($parser->canHandle($item)) {
                return $parser->parse($item);
            }
        }

        throw new \Exception(sprintf('Cannot export item of type [%s].', get_class($item)));
    }
}
