<?php

namespace App\Services\Archive\Parser\Parsers;

use App\Models\Route;
use App\Models\File;
use App\Services\Archive\ParseResult;
use App\Services\Archive\ParseResults;
use App\Services\Archive\Parser\FileResource;
use App\Services\Archive\Contracts\Parser;
use App\Services\Archive\Traits\CreatesParseResult;

class RouteParser implements Parser
{
    use CreatesParseResult;

    public function canHandle($item): bool
    {
        return $item instanceof Route;
    }

    /**
     * @param Route $item
     * @return FileResource[]
     */
    public function parse($item): ParseResult
    {
        $this->addMetaData('route', $item->toArray());
        $routeFile = $item->routeFile()->first();
        if($routeFile) {
            $this->addFile($this->parseRouteFile($item->id, $routeFile));
        }
        foreach($item->files()->get() as $file) {
            $this->addFile($this->parseRouteMediaFile($item->id, $file));
        }
        return $this->result();
    }

    private function parseRouteFile(int $routeId, File $routeFile): FileResource
    {
        return FileResource::new($routeFile)
            ->setNewName(sprintf('Route_%u_Route_File_%u.%s', $routeId, $routeFile->id, $routeFile->extension));
    }

    private function parseRouteMediaFile(int $routeId, File $mediaFile): FileResource
    {
        return FileResource::new($mediaFile)
            ->setNewName(sprintf('Route_%u_Media_File_%u.%s', $routeId, $mediaFile->id, $mediaFile->extension));
    }
}