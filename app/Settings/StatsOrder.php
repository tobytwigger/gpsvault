<?php

namespace App\Settings;

use FormSchema\Schema\Field;
use Settings\Types\UserSetting;

class StatsOrder extends UserSetting
{

    public function defaultValue(): mixed
    {
        return ['php', 'strava'];
    }

    public function fieldOptions(): Field
    {
        return \FormSchema\Generator\Field::number('test');
    }

    public function rules(): array|string
    {
        return 'array';
    }

    protected function groups(): array
    {
        return ['general'];
    }
}
