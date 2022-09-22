<?php

namespace App\Integrations\Strava\Import\Upload\Zip;

use Illuminate\Support\Str;

class ZipFile
{

    private string $filename;

    public function __construct(string $filename)
    {
        $this->filename = $filename;
    }

    public function isActivityFile(): bool
    {
        return Str::startsWith($this->filename, 'activities/') && Str::length($this->filename) > 11;
    }

    public function isPhoto(): bool
    {
        return Str::startsWith($this->filename, 'photos/') && Str::length($this->filename) > 7;
    }

    public function getFilenameWithoutExtAfter(string $prefix): string
    {
        return (string) Str::of($this->filename)->after($prefix)->before('.');
    }

    public function isTarFile(): bool
    {
        return Str::endsWith($this->filename, '.gz');
    }

    public function __toString(): string
    {
        return $this->filename;
    }

    public function getFilename(): string
    {
        return $this->filename;
    }

}
