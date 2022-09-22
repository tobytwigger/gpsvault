<?php

namespace App\Integrations\Strava\Import\Upload\Zip;

use Illuminate\Support\Collection;

class ZipContents
{
    private Collection $zipContents;

    public function __construct(array $zipContents)
    {
        $this->zipContents = collect($zipContents)->map(fn (string $path) => new ZipFile($path));
    }

    public function activityFiles(): Collection
    {
        return $this->zipContents
            ->filter(fn (ZipFile $filename) => $filename->isActivityFile())
            ->values();
    }

    public function photos(): Collection
    {
        return $this->zipContents
            ->filter(fn (ZipFile $filename) => $filename->isPhoto())
            ->values();
    }

    public function photoCsv(): ?ZipFile
    {
        return $this->zipContents
            ->filter(fn (ZipFile $file) => $file->getFilename() === 'photos.csv')
            ->first();
    }
}
