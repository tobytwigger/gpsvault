<?php

namespace App\Services\Filepond;

use Illuminate\Contracts\Validation\InvokableRule;
use Illuminate\Support\Facades\Storage;
use Sopamo\LaravelFilepond\Filepond;

class FilepondRule implements InvokableRule
{
    public function __invoke($attribute, $value, $fail)
    {
        if (!$this->fileExists($value)) {
            $fail('The :attribute was not uploaded successfully');
        }
    }

    protected function fileExists($serverId): bool
    {
        return Storage::disk(config('filepond.temporary_files_disk'))->exists(
            app(Filepond::class)->getPathFromServerId($serverId)
        );
    }
}
