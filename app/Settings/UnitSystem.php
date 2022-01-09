<?php

namespace App\Settings;

use FormSchema\Schema\Field;
use Settings\Types\UserSetting;

class UnitSystem extends UserSetting
{

    public function defaultValue(): mixed
    {
        return 'metric';
    }

    public function fieldOptions(): Field
    {
        return \FormSchema\Generator\Field::number('test');
    }

    public function rules(): array|string
    {
        return 'string|in:metric,imperial';
    }

    protected function groups(): array
    {
        return ['general'];
    }
}
