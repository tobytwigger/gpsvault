<?php

namespace App\Settings;

use FormSchema\Schema\Field;
use Settings\Types\UserSetting;

class DarkMode extends UserSetting
{

    public function alias(): ?string
    {
        return 'dark_mode';
    }

    public function defaultValue(): mixed
    {
        return false;
    }

    public function fieldOptions(): Field
    {
        return \FormSchema\Generator\Field::switch(static::class);
    }

    public function rules(): array|string
    {
        return 'boolean';
    }

    protected function groups(): array
    {
        return ['general', 'appearance'];
    }
}
