<?php

namespace App\Integrations\Strava\Import\Upload;


use App\Integrations\Strava\Import\Upload\Importers\ImportResults;
use App\Integrations\Strava\Import\Upload\Models\StravaImport;
use App\Integrations\Strava\Import\Upload\Models\StravaImportResult;
use App\Integrations\Strava\Import\Upload\Zip\ImportZip;
use App\Models\User;

class StravaImporter
{
    private array $importers = [];

    public function registerImporter(string $importer)
    {
        if (!is_a($importer, \App\Integrations\Strava\Import\Upload\Importers\Importer::class, true)) {
            throw new \Exception(sprintf('Importer [%s] must extend the Importer contract', $importer));
        }
        $this->importers[] = $importer;
    }

    /**
     * @return array|\App\Integrations\Strava\Import\Upload\Importers\Importer[]
     */
    public function importers(): array
    {
        return collect($this->importers)
            ->map(fn (string $importer) => app($importer))
            ->all();
    }

    public function import(ImportZip $zip, User $user): StravaImport
    {
        $import = StravaImport::create(['user_id' => $user->id]);
        $results = new ImportResults();
        foreach ($this->importers() as $importer) {
            $results->merge(
                $importer->run($zip, $user)
            );
        }
        foreach ($results->all() as $result) {
            StravaImportResult::saveResult($import, $result['type'], $result['message'], $result['success'], $result['data']);
        }

        return $import;
    }
}
