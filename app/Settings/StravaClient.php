<?php

namespace App\Settings;

use FormSchema\Schema\Field;
use Settings\Types\GlobalSetting;

class StravaClient extends GlobalSetting
{

    public function defaultValue(): mixed
    {
        return null;
    }

    public function fieldOptions(): Field
    {
        return \FormSchema\Generator\Field::text('');
    }

    public function rules(): array|string
    {
        return 'nullable|integer|exists:strava_clients';
    }

    protected function groups(): array
    {
        return [];
    }
}
