<?php

namespace App\Services\Filepond;

use Illuminate\Contracts\Validation\InvokableRule;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Sopamo\LaravelFilepond\Filepond;

class FilepondRule implements InvokableRule
{
    public function __invoke($attribute, $value, $fail)
    {
        $this->validateStructure($value, $fail);

        $this->validateFileExistence($value, $fail);
    }

    protected function validateStructure($value, \Closure $fail): void
    {
        try {
            Validator::validate($value, [
                'filename' => 'required|string',
                'size' => 'required|max:1000000000',
                'mimetype' => 'required|string',
                'serverId' => 'required|string',
                'extension' => 'required|string'
            ]);
        } catch (ValidationException $e) {
            $fail($e->validator->errors()->first());
        }
    }

    private function validateFileExistence(array $value, \Closure $fail)
    {
        if (!Storage::disk(config('filepond.temporary_files_disk'))->exists(
            app(Filepond::class)->getPathFromServerId($value['serverId'])
        )) {
            $fail('The :attribute was not uploaded successfully');
        }
    }
}
