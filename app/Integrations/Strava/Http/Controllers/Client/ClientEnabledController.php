<?php

namespace App\Integrations\Strava\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Integrations\Strava\Client\Models\StravaClient;
use Illuminate\Support\Facades\Auth;

class ClientEnabledController extends Controller
{
    public function enable(StravaClient $client)
    {
        abort_if($client->user_id !== Auth::id(), 403, 'You can only turn on a client you own.');

        $client->enabled = true;
        $client->save();

        return redirect()->route('integration.strava');
    }

    public function disable(StravaClient $client)
    {
        abort_if($client->user_id !== Auth::id(), 403, 'You can only turn off a client you own.');

        $client->enabled = false;
        $client->save();

        return redirect()->route('integration.strava');
    }
}
