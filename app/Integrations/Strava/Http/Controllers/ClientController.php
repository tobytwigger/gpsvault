<?php

namespace App\Integrations\Strava\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Integrations\Strava\Models\StravaClient;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ClientController extends Controller
{

    public function index()
    {
        return Inertia::render('Strava/Client/Index', [
            'ownedClients' => Auth::user()->ownedClients()->get()->map(function(StravaClient $client) {
                return array_merge($client->toArray(), [
                    'client_id' => $client->client_id,
                    'client_secret' => $client->client_secret,
                ]);
            }),
            'sharedClients' => Auth::user()->sharedClients()->get()->map(function(StravaClient $client) {
                return [
                    'id' => $client->id,
                    'user' => $client->owner->name,
                    'enabled' => $client->enabled,
                    'used_15_min_calls' => $client->used_15_min_calls,
                    'used_daily_calls' => $client->used_daily_calls,
                    'pending_calls' => $client->pending_calls,
                    'is_connected' => $client->is_connected,
                ];
            }),
            'publicClients' => StravaClient::where('public', true)->enabled()->where('user_id', '!=', Auth::id())->get()->map(function(StravaClient $client) {
                return [
                    'id' => $client->id,
                    'used_15_min_calls' => $client->used_15_min_calls,
                    'used_daily_calls' => $client->used_daily_calls,
                    'pending_calls' => $client->pending_calls,
                    'is_connected' => $client->is_connected,
                ];
            }),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'client_id' => 'required|integer',
            'client_secret' => 'required|string'
        ]);

        StravaClient::create($request->only(['client_id', 'client_secret']));

        return redirect()->route('strava.client.index');
    }

    public function destroy(StravaClient $client)
    {
        $client->delete();

        return redirect()->route('strava.client.index');
    }

}
