<?php

namespace App\Settings;

use FormSchema\Schema\Field;
use Illuminate\Support\Facades\Auth;
use Settings\Types\GlobalSetting;

class BruitAPIKey extends GlobalSetting
{

    public function canWrite(): bool
    {
        return Auth::check() && Auth::user()->can('manage-bruit-key');
    }

    public function alias(): ?string
    {
        return 'bruit_api_key';
    }

    public function defaultValue(): mixed
    {
        return config('services.bruit.key');
    }

    public function rules(): array|string
    {
        return 'string|min:1';
    }

    protected function groups(): array
    {
        return [];
    }
}
