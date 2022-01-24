<?php

namespace App\Settings;

use FormSchema\Schema\Field;
use Settings\Types\UserSetting;

class BruitAPIKey extends UserSetting
{

    public function defaultValue(): mixed
    {
        return config('services.bruit.key');
    }

    public function fieldOptions(): Field
    {
        return \FormSchema\Generator\Field::text('test');
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
