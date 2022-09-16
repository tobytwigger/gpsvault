<?php

namespace App\Integrations\Strava\Import\Upload\Importers;

use App\Models\User;

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

    abstract protected function import();

    abstract public function type(): string;
}
