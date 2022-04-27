<?php

namespace App\Services\Archive\Parser\Parsers;

use App\Models\File;
use App\Models\Route;
use App\Services\Archive\Contracts\Parser;
use App\Services\Archive\Parser\FileResource;
use App\Services\Archive\ParseResult;
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
        $file = $item->file()->first();
        if ($file) {
            $this->addFile($this->parseRouteFile($item->id, $file));
        }
        foreach ($item->files()->get() as $file) {
            $this->addFile($this->parseRouteMediaFile($item->id, $file));
        }

        return $this->result();
    }

    private function parseRouteFile(int $routeId, File $file): FileResource
    {
        return FileResource::new($file)
            ->setNewName(sprintf('Route_%u_Route_File_%u.%s', $routeId, $file->id, $file->extension));
    }

    private function parseRouteMediaFile(int $routeId, File $mediaFile): FileResource
    {
        return FileResource::new($mediaFile)
            ->setNewName(sprintf('Route_%u_Media_File_%u.%s', $routeId, $mediaFile->id, $mediaFile->extension));
    }
}
