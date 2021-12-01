<?php

namespace App\Integrations\Strava\Import\Importers;

class ImportResults
{

    protected array $results = [];

    public function addResult(string $type, string $message, bool $success)
    {
        $this->results[] = [
            'type' => $type,
            'message' => $message,
            'success' => $success
        ];
    }

    public function all(): array
    {
        return $this->results;
    }

    public function merge(ImportResults $importResult): ImportResults
    {
        $this->results = array_merge($this->results, $importResult->all());
        return $this;

    }

}
