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

    public function store(Request $request)
    {
        $request->validate([
            'client_id' => 'required|integer',
            'client_secret' => 'required|string',
            'name' => 'sometimes|nullable|string|max:255',
            'description' => 'sometimes|nullable|string|max:65535',
        ]);

        StravaClient::create($request->only(['client_id', 'client_secret', 'name', 'description']));

        return redirect()->route('integration.strava');
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

        return redirect()->route('integration.strava');
    }

    public function destroy(StravaClient $client)
    {
        abort_if($client->user_id !== Auth::id(), 403, 'You can only delete a client you own.');

        $client->delete();

        return redirect()->route('integration.strava');
    }
}
