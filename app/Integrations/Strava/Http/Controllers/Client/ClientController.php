<?php

namespace App\Integrations\Strava\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Integrations\Strava\Client\Models\StravaClient;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ClientController extends Controller
{
    public function index()
    {
        $ownedClients = Auth::user()->ownedClients()->with('sharedUsers')->paginate(
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
                'pending_calls' => $client->pending_calls,
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
                'pending_calls' => $client->pending_calls,
                'is_connected' => $client->is_connected,
            ];
            $publicClients->offsetSet($index, $client);
        }

        return Inertia::render('Integrations/Strava/Client/Index', [
            'ownedClients' => $ownedClients,
            'sharedClients' => $sharedClients,
            'publicClients' => $publicClients,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'client_id' => 'required|integer',
            'client_secret' => 'required|string',
            'name' => 'sometimes|nullable|string|max:255',
            'description' => 'sometimes|nullable|string|max:65535',
        ]);

        StravaClient::create($request->only(['client_id', 'client_secret', 'name', 'description']));

        return redirect()->route('strava.client.index');
    }

    public function update(Request $request, StravaClient $client)
    {
        abort_if($client->user_id !== Auth::id(), 403, 'You can only update a client you own.');

        $request->validate([
            'client_secret' => 'sometimes|string',
            'name' => 'sometimes|nullable|string|max:255',
            'description' => 'sometimes|nullable|string|max:65535',
        ]);

        $client->fill($request->only(['client_secret', 'name', 'description']));
        $client->save();

        return redirect()->route('strava.client.index');
    }

    public function destroy(StravaClient $client)
    {
        abort_if($client->user_id !== Auth::id(), 403, 'You can only delete a client you own.');

        $client->delete();

        return redirect()->route('strava.client.index');
    }
}
