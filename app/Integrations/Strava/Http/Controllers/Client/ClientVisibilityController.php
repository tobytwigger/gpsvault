<?php

namespace App\Integrations\Strava\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Integrations\Strava\Client\Models\StravaClient;
use Illuminate\Support\Facades\Auth;

class ClientVisibilityController extends Controller
{

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
