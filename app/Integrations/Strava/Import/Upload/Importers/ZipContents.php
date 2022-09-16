<?php

namespace App\Integrations\Strava\Import\Upload\Importers;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class ZipContents
{
    private Collection $zipContents;

    public function __construct(array $zipContents)
    {
        $this->zipContents = collect($zipContents)->map(fn(string $path) => new ZipFile($path));
    }

    public function activityFiles(): Collection
    {
        return $this->zipContents
            ->filter(fn (ZipFile $filename) => $filename->isActivityFile())
            ->values();
    }

}
