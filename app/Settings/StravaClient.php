<?php

namespace App\Settings;

use App\Integrations\Strava\Client\Exceptions\ClientNotAvailable;
use FormSchema\Schema\Field;
use Illuminate\Database\Eloquent\ModelNotFoundException;
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

    /**
     * @param int|null $id The ID of the model to query against
     * @throws ModelNotFoundException
     */
    public static function getClientModelOrFail(int $id = null): \App\Integrations\Strava\Models\StravaClient
    {
        $clientId = static::getValue($id);
        if($clientId === null) {
            throw new ClientNotAvailable('No system client has been set');
        }
        return \App\Integrations\Strava\Models\StravaClient::findOrFail($clientId);
    }

    public function rules(): array|string
    {
        return 'nullable|integer|exists:strava_clients,id';
    }

    protected function groups(): array
    {
        return [];
    }
}
