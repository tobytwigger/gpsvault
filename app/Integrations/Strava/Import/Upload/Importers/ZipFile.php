<?php

namespace App\Integrations\Strava\Import\Upload\Importers;

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

    public function __toString(): string
    {
        return $this->filename;
    }

}
