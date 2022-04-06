<?php

namespace App\Settings;

use FormSchema\Schema\Field;
use Settings\Types\UserSetting;

class StatsOrder extends UserSetting
{

    public function alias(): ?string
    {
        return 'stats_order_preference';
    }

    public function defaultValue(): mixed
    {
        return ['php', 'strava'];
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
