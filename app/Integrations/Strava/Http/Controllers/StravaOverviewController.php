<?php

namespace App\Integrations\Strava\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Integrations\Strava\Client\Exceptions\ClientNotAvailable;
use App\Integrations\Strava\Client\Models\StravaClient;
use App\Integrations\Strava\Import\Upload\Models\StravaImport;
use App\Integrations\Strava\Sync\SyncStatus;
use App\Models\Activity;
use App\Models\File;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Inertia\Inertia;

class StravaOverviewController extends Controller
{
    public function index()
    {
        if (Auth::user()->can('manage-strava-clients')) {
            $ownedClients = Auth::user()->ownedClients()->with('sharedUsers')->orderBy('created_at')->pluck('strava_clients.id');
            $sharedClients = Auth::user()->sharedClients()->pluck('strava_clients.id');
            $publicClients = StravaClient::public()->enabled()->where('user_id', '!=', Auth::id())->pluck('strava_clients.id');
            $clientIds = $ownedClients->merge($sharedClients)->merge($publicClients);

            $isAvailable = StravaClient::enabled()->whereIn('id', $clientIds)->exists();
            $isConnected = StravaClient::enabled()->connected(Auth::id())->whereIn('id', $clientIds)->exists();
            $clientHasSpace = StravaClient::enabled()->connected(Auth::id())->withSpaces()->whereIn('id', $clientIds)->exists();
            $defaultClient = StravaClient::enabled()->whereIn('id', $clientIds)->first();
        } else {
            try {
                $defaultClient = \App\Settings\StravaClient::getClientModelOrFail();
                $isAvailable = $defaultClient->enabled;
                $isConnected = $defaultClient->enabled && $defaultClient->is_connected;
                $clientHasSpace = $defaultClient->hasSpaces();
            } catch (ClientNotAvailable) {
                $isAvailable = false;
                $isConnected = false;
                $clientHasSpace = false;
                $defaultClient = null;
            }
        }
        if (Auth::user()->can('manage-strava-clients')) {
            $ownedClients = Auth::user()->ownedClients()->with('sharedUsers')->orderBy('created_at')->pluck('strava_clients.id');
            $sharedClients = Auth::user()->sharedClients()->pluck('strava_clients.id');
            $publicClients = StravaClient::public()->enabled()->where('user_id', '!=', Auth::id())->pluck('strava_clients.id');
            $clientIds = $ownedClients->merge($sharedClients)->merge($publicClients);

            $clientPaginator = StravaClient::whereIn('id', $clientIds)->orderByRaw(sprintf('array_position(ARRAY[%s]::bigint[], id)', $clientIds->join(', ')))
                ->paginate(request()->input('perPage', 10))
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
            if ($defaultClient === null) {
                $clientPaginator = new LengthAwarePaginator([], 0, request()->input('perPage', 1));
            } else {
                $clientPaginator = new LengthAwarePaginator([$defaultClient], 1, request()->input('perPage', 1));
            }
        }

        return Inertia::render('Integrations/Strava/Index', [
            'isAvailable' => $isAvailable,
            'isConnected' => $isConnected,
            'clientHasSpace' => $clientHasSpace,
            'defaultClient' => $defaultClient,
            'clients' => $clientPaginator,
            'sync' => SyncStatus::forUser(Auth::user())->toArray(),
            'imports' => StravaImport::where('user_id', Auth::id())->latest()->get(),
            // Activities that are linked to Strava but don't have a file.
            'activity_files_needing_import' => Activity::whereHas('additionalData', fn (Builder $subquery) => $subquery->where('key', 'strava_id')->whereNotNull('value'))
                ->whereNull('file_id')->get()->map(fn (Activity $activity) => ['id' => $activity->id, 'name' => $activity->name]),
            // Activities that are linked to Strava and are missing a photo. This means a photo ID referenced does not exist in files.
            'photos_needing_import' => Activity::whereHas('additionalData', fn (Builder $subquery) => $subquery->where('key', 'strava_id')->whereNotNull('value'))
                ->with(['files'])->get()->map(function (Activity $activity) {
                    $photos = [];
                    foreach (Arr::wrap($activity->getAdditionalData('strava_photo_ids')) as $photoId) {
                        if ($activity->files->filter(fn (File $file) => Str::contains($file->filename, $photoId))->count() === 0) {
                            $photos[] = $photoId;
                        }
                    }
                    if (empty($photos)) {
                        return null;
                    }

                    return ['id' => $activity->id, 'name' => $activity->name, 'photos' => $photos];
                })->filter()->values(),
        ]);
    }
}
