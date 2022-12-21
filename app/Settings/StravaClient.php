<?php

namespace App\Settings;

use App\Integrations\Strava\Client\Exceptions\ClientNotAvailable;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Settings\Types\GlobalSetting;

class StravaClient extends GlobalSetting
{
    public function canWrite(): bool
    {
        return Auth::check() && Auth::user()->can('manage-global-settings');
    }


    public function alias(): ?string
    {
        return 'strava_client_id';
    }

    public function defaultValue(): mixed
    {
        return null;
    }

    /**
     * @param int|null $id The ID of the model to query against
     * @throws ModelNotFoundException
     */
    public static function getClientModelOrFail(?int $id = null): \App\Integrations\Strava\Client\Models\StravaClient
    {
        $clientId = static::getValue($id);
        if ($clientId === null) {
            throw new ClientNotAvailable('No system client has been set.');
        }

        return \App\Integrations\Strava\Client\Models\StravaClient::find($clientId) ?? throw new ClientNotAvailable('No system client has been set.');
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
