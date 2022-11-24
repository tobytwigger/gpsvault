<?php

namespace App\Services\Filepond;

use Illuminate\Contracts\Validation\InvokableRule;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Sopamo\LaravelFilepond\Filepond;

class FilepondRule implements InvokableRule
{
    private \Closure $fail;

    public function __invoke($attribute, $value, $fail)
    {
        $this->fail = $fail;

        try {
            $this->validateStructure($value, $fail);

            $this->validateFileExistence($value, $fail);
        } catch (\Exception $e) {
            if ($e->getMessage() === 'Rule failed') {
                return;
            }

            throw $e;
        }
    }

    private function failRule(string $message)
    {
        call_user_func($this->fail, $message);

        throw new \Exception('Rule failed');
    }

    protected function validateStructure($value): void
    {
        if (!is_array($value)) {
            $this->failRule('The :attribute is not an array');
        }

        try {
            Validator::validate($value, [
                'filename' => 'required|string',
                'size' => 'required|max:1000000000',
                'mimetype' => 'required|string',
                'serverId' => 'required|string',
                'extension' => 'required|string',
            ]);
        } catch (ValidationException $e) {
            $this->failRule($e->validator->errors()->first());
        }
    }

    private function validateFileExistence($value)
    {
        if (!Storage::disk(config('filepond.temporary_files_disk'))->exists(
            app(Filepond::class)->getPathFromServerId($value['serverId'])
        )) {
            $fail('The :attribute was not uploaded successfully');
        }
    }
}
