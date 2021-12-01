<?php

namespace App\Integrations\Strava\Import\Importers;

use App\Models\File;
use App\Models\SyncTask;
use App\Models\User;

abstract class Importer
{

    protected ImportingZip $zip;

    private ?SyncTask $task;

    private ImportResults $importResults;

    protected User $user;

    public function run(ImportingZip $zip, ?SyncTask $task, User $user): ImportResults
    {
        $this->user = $user;
        $this->importResults = new ImportResults();
        $this->zip = $zip;
        $this->task = $task;
        try {
            $this->import();
        } catch (\Exception $e) {
            $this->failed($e->getMessage(), $this->type());
        }
        return $this->importResults;
    }

    public function statusUpdate(string $message)
    {
        if($this->task !== null) {
            $this->task->addMessage($message);
        }
    }

    public function succeeded(string $message, ?string $type = null)
    {
        $this->importResults->addResult($type ?? $this->type(), $message, true);
    }

    public function failed(string $message, ?string $type = null)
    {
        $this->importResults->addResult($type ?? $this->type(), $message, false);
    }

    protected function updateProgress(int $number, int $outOf)
    {
        if($number%20 === 0) {
            $this->statusUpdate(sprintf('Extracting %u/%u activities', $number, $outOf));
        }
    }

    abstract protected function import();

    abstract public function type(): string;

}
