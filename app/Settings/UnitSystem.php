<?php

namespace App\Settings;

use Settings\Types\UserSetting;

class UnitSystem extends UserSetting
{
    public function alias(): ?string
    {
        return 'unit_system';
    }

    public function defaultValue(): mixed
    {
        return 'metric';
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
