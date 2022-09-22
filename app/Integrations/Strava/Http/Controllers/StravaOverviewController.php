<?php

namespace App\Integrations\Strava\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Integrations\Strava\Client\Models\StravaClient;
use App\Integrations\Strava\Import\Upload\Models\StravaImport;
use App\Integrations\Strava\Sync\SyncStatus;
use App\Models\Activity;
use App\Models\File;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Inertia\Inertia;

class StravaOverviewController extends Controller
{
    public function index()
    {
        $ownedClients = Auth::user()->ownedClients()->with('sharedUsers')->orderBy('created_at')->paginate(
            perPage: request()->input('owned_per_page', 8),
            columns: ['*'],
            pageName: 'owned_page',
        );
        foreach ($ownedClients->items() as $index => $client) {
            $client = array_merge($client->toArray(), [
                'client_id' => $client->client_id,
                'client_secret' => $client->client_secret,
                'created_at' => $client->created_at->toIso8601String(),
                'updated_at' => $client->updated_at->toIso8601String(),
                'shared_users' => $client->sharedUsers->map(fn (User $user) => [
                    'id' => $user->id, 'name' => $user->name, 'email' => $user->email,
                ]),
            ]);
            $ownedClients->offsetSet($index, $client);
        }

        $sharedClients = Auth::user()->sharedClients()->paginate(
            perPage: request()->input('shared_per_page', 8),
            columns: ['*'],
            pageName: 'shared_page',
        );
        foreach ($sharedClients->items() as $index => $client) {
            $client = [
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
            $sharedClients->offsetSet($index, $client);
        }

        $publicClients = StravaClient::public()->enabled()->where('user_id', '!=', Auth::id())->paginate(
            perPage: request()->input('public_per_page', 8),
            columns: ['*'],
            pageName: 'public_page',
        );
        foreach ($publicClients->items() as $index => $client) {
            $client =[
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
            $publicClients->offsetSet($index, $client);
        }

        return Inertia::render('Integrations/Strava/Index', [
            'ownedClients' => $ownedClients,
            'sharedClients' => $sharedClients,
            'publicClients' => $publicClients,
            'sync' => SyncStatus::forUser(Auth::user())->toArray(),
            'imports' => StravaImport::where('user_id', Auth::id())->latest()->get(),
            // Activities that are linked to Strava but don't have a file.
            'activity_files_needing_import' => Activity::whereHas('additionalData', fn (Builder $subquery) => $subquery->where('key', 'strava_id')->whereNotNull('value'))
                ->whereNull('file_id')->get()->map(fn (Activity $activity) => ['id' => $activity->id, 'name' => $activity->name]),
            // Activities that are linked to Strava and are missing a photo. This means a photo ID referenced does not exist in files.
            'photos_needing_import' => Activity::whereHas('additionalData', fn (Builder $subquery) => $subquery->where('key', 'strava_id')->whereNotNull('value'))
                ->with(['files'])->get()->map(function (Activity $activity) {
                    $photos = [];
                    foreach ($activity->getAdditionalData('strava_photo_ids') ?? [] as $photoId) {
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
