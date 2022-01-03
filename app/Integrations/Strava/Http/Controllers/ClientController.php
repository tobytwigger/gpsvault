<?php

namespace App\Integrations\Strava\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Integrations\Strava\Models\StravaClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ClientController extends Controller
{

    public function index()
    {
        return Inertia::render('Strava/Client/Index', [
            'clients' => Auth::user()->stravaClients()
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
