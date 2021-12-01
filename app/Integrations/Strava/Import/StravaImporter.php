<?php

namespace App\Integrations\Strava\Import;

use App\Integrations\Strava\Import\Importers\ImportingZip;
use App\Integrations\Strava\Import\Importers\ImportResults;
use App\Integrations\Strava\Import\Models\Import;
use App\Integrations\Strava\Import\Models\ImportResult;
use App\Models\SyncTask;
use App\Models\User;

class StravaImporter
{

    private array $importers = [];

    public function registerImporter(string $importer)
    {
        if(!is_a($importer, \App\Integrations\Strava\Import\Importers\Importer::class, true)) {
            throw new \Exception(sprintf('Importer [%s] must extend the Importer contract', $importer));
        }
        $this->importers[] = $importer;
    }

    public function importers(): array
    {
        return collect($this->importers)
            ->map(fn(string $importer) => app($importer))
            ->all();
    }

    public function import(ImportingZip $zip, ?SyncTask $syncTask, User $user): Import
    {
        $results = new ImportResults();
        foreach($this->importers() as $importer) {
            if($syncTask) {
                $syncTask->addMessage(sprintf('Importing %s.', $importer->type()));
            }
            $results->merge(
                $importer->run($zip, $syncTask, $user)
            );
        }
        $import = Import::create();
        foreach($results->all() as $result) {
            ImportResult::saveResult($import, $result['type'], $result['message'], $result['success']);
        }
        $zip->clearUp();
        return $import;
    }

}
