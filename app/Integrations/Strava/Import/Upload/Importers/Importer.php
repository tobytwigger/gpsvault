<?php

namespace App\Integrations\Strava\Import\Upload\Importers;

use App\Integrations\Strava\Import\Upload\Zip\ImportZip;
use App\Integrations\Strava\Import\Upload\Zip\ZipFile;
use App\Models\File;
use App\Models\User;
use App\Services\File\Upload;
use Illuminate\Support\Str;

abstract class Importer
{
    protected ImportZip $zip;

    private ImportResults $importResults;

    protected User $user;

    public function run(ImportZip $zip, User $user): ImportResults
    {
        $this->user = $user;
        $this->importResults = new ImportResults();
        $this->zip = $zip;

        try {
            $this->import();
        } catch (\Exception $e) {
            $this->failed($e->getMessage(), [], $this->type());
        }

        return $this->importResults;
    }

    public function succeeded(string $message, array $data = [], ?string $type = null)
    {
        $this->importResults->addResult($type ?? $this->type(), $message, true, $data);
    }

    public function failed(string $message, array $data = [], ?string $type = null)
    {
        $this->importResults->addResult($type ?? $this->type(), $message, false, $data);
    }

    public function convertToFile(ZipFile $filename, string $type): File
    {
        $contents = $this->zip->extract($filename);
        $filenameAsString = (string) $filename;

        if ($filename->isTarFile()) {
            $filenameAsString = Str::before($filenameAsString, '.gz');
            $contents = gzdecode($contents);
        }

        return Upload::withContents(
            trim($contents),
            $this->sanitiseFileName($filenameAsString),
            $this->user,
            $type
        );
    }

    public function sanitiseFileName(string $filename)
    {
        return Str::replace(['/', '\\'], ['_', '-'], $filename);
    }

    abstract protected function import();

    abstract public function type(): string;
}
