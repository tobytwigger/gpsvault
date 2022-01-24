<?php

namespace App\Integrations\Strava\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Integrations\Strava\Models\StravaClient;
use Illuminate\Support\Facades\Auth;

class ClientStatusController extends Controller
{

    public function enable(StravaClient $client)
    {
        abort_if($client->user_id !== Auth::id(), 403, 'You can only turn on a client you own.');

        $client->enabled = true;
        $client->save();

        return redirect()->route('strava.client.index');
    }

    public function disable(StravaClient $client)
    {
        abort_if($client->user_id !== Auth::id(), 403, 'You can only turn off a client you own.');

        $client->enabled = false;
        $client->save();

        return redirect()->route('strava.client.index');
    }

    public function makePublic(StravaClient $client)
    {
        abort_if($client->user_id !== Auth::id(), 403, 'You can only make a client that you own public.');

        $client->public = true;
        $client->save();

        return redirect()->route('strava.client.index');
    }

    public function makePrivate(StravaClient $client)
    {
        abort_if($client->user_id !== Auth::id(), 403, 'You can only make a client that you own private.');

        $client->public = false;
        $client->save();

        return redirect()->route('strava.client.index');
    }

}
