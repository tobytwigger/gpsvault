<?php

namespace App\Integrations\Strava\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Integrations\Strava\Models\StravaClient;

class ClientStatusController extends Controller
{

    public function enable(StravaClient $client)
    {
        $client->enabled = true;
        $client->save();

        return redirect()->route('strava.client.index');
    }

    public function disable(StravaClient $client)
    {
        $client->enabled = false;
        $client->save();

        return redirect()->route('strava.client.index');
    }

    public function makePublic(StravaClient $client)
    {
        $client->public = true;
        $client->save();

        return redirect()->route('strava.client.index');
    }

    public function makePrivate(StravaClient $client)
    {
        $client->public = false;
        $client->save();

        return redirect()->route('strava.client.index');
    }

}
