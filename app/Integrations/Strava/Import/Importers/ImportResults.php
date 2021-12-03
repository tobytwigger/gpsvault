<?php

namespace App\Integrations\Strava\Import\Importers;

class ImportResults
{

    protected array $results = [];

    public function addResult(string $type, string $message, bool $success, array $data = [])
    {
        $this->results[] = [
            'type' => $type,
            'message' => $message,
            'success' => $success,
            'data' => $data
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
