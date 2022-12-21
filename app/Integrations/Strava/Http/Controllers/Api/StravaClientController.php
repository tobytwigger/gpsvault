<?php

namespace App\Integrations\Strava\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Integrations\Strava\Client\Exceptions\ClientNotAvailable;
use App\Integrations\Strava\Client\Models\StravaClient;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class StravaClientController extends Controller
{

    public function index()
    {
        if (Auth::user()->can('manage-strava-clients')) {
            $ownedClients = Auth::user()->ownedClients()->with('sharedUsers')->orderBy('created_at')->pluck('strava_clients.id');
            $sharedClients = Auth::user()->sharedClients()->pluck('strava_clients.id');
            $publicClients = StravaClient::public()->enabled()->where('user_id', '!=', Auth::id())->pluck('strava_clients.id');
            $clientIds = $ownedClients->merge($sharedClients)->merge($publicClients);

            return StravaClient::whereIn('id', $clientIds)->orderByRaw(sprintf('array_position(ARRAY[%s]::bigint[], id)', $clientIds->join(', ')))
                ->paginate(request()->input('perPage', 15))
                ->through(function (StravaClient $client) use ($ownedClients, $sharedClients) {
                    if ($ownedClients->contains($client->id)) {
                        return array_merge([
                            'type' => 'owned',
                            'client_id' => $client->client_id,
                            'client_secret' => $client->client_secret,
                            'created_at' => $client->created_at->toIso8601String(),
                            'updated_at' => $client->updated_at->toIso8601String(),
                            'shared_users' => $client->sharedUsers->map(fn (User $user) => [
                                'id' => $user->id, 'name' => $user->name, 'email' => $user->email,
                            ]),
                        ], $client->toArray());
                    } elseif ($sharedClients->contains($client->id)) {
                        return [
                            'type' => 'shared',
                            'id' => $client->id,
                            'name' => $client->name,
                            'client_id' => $client->client_id,
                            'description' => $client->description,
                            'user' => $client->owner->name,
                            'enabled' => $client->enabled,
                            'used_15_min_calls' => $client->used_15_min_calls,
                            'used_daily_calls' => $client->used_daily_calls,
                            'limit_15_min' => $client->limit_15_min,
                            'limit_daily' => $client->limit_daily,
                            'is_connected' => $client->is_connected,
                        ];
                    }

                    return [
                        'type' => 'public',
                        'id' => $client->id,
                        'name' => $client->name,
                        'client_id' => $client->client_id,
                        'description' => $client->description,
                        'used_15_min_calls' => $client->used_15_min_calls,
                        'used_daily_calls' => $client->used_daily_calls,
                        'limit_15_min' => $client->limit_15_min,
                        'limit_daily' => $client->limit_daily,
                        'is_connected' => $client->is_connected,
                    ];
                });
        } else {
            return new LengthAwarePaginator([Auth::user()->availableClient()], 1, request()->input('perPage'));
        }
    }

    public function show(StravaClient $client)
    {
        $canAccess = false;
        if (Auth::user()->can('manage-strava-clients')) {
            $canAccess = $client->user_id === Auth::id()
                || $client->sharedUsers()->where('users.id', Auth::id())->exists()
                || ($client->public && $client->enabled);
        } else {
            try {
                $canAccess = \App\Settings\StravaClient::getClientModelOrFail(Auth::id())->is($client);
            } catch (ClientNotAvailable) {
                $canAccess = false;
            }
        }

        abort_if(!$canAccess, 403, 'You do not have access to view this client.');

        if ($client->user_id === Auth::id()) {
            return array_merge([
                'type' => 'owned',
                'client_id' => $client->client_id,
                'client_secret' => $client->client_secret,
                'created_at' => $client->created_at->toIso8601String(),
                'updated_at' => $client->updated_at->toIso8601String(),
                'shared_users' => $client->sharedUsers->map(fn (User $user) => [
                    'id' => $user->id, 'name' => $user->name, 'email' => $user->email,
                ]),
            ], $client->toArray());
        } elseif ($client->sharedUsers()->where('users.id', Auth::id())->exists()) {
            return [
                'type' => 'shared',
                'id' => $client->id,
                'name' => $client->name,
                'client_id' => $client->client_id,
                'description' => $client->description,
                'user' => $client->owner->name,
                'enabled' => $client->enabled,
                'used_15_min_calls' => $client->used_15_min_calls,
                'used_daily_calls' => $client->used_daily_calls,
                'limit_15_min' => $client->limit_15_min,
                'limit_daily' => $client->limit_daily,
                'is_connected' => $client->is_connected,
            ];
        }

        return [
            'type' => 'public',
            'id' => $client->id,
            'name' => $client->name,
            'client_id' => $client->client_id,
            'description' => $client->description,
            'used_15_min_calls' => $client->used_15_min_calls,
            'used_daily_calls' => $client->used_daily_calls,
            'limit_15_min' => $client->limit_15_min,
            'limit_daily' => $client->limit_daily,
            'is_connected' => $client->is_connected,
        ];
    }
}
