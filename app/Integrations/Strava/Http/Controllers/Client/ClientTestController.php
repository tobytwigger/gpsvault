<?php

namespace App\Integrations\Strava\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Integrations\Strava\Client\Models\StravaClient;
use App\Integrations\Strava\Client\Strava;
use Illuminate\Support\Facades\Auth;

class ClientTestController extends Controller
{
    public function __invoke(StravaClient $client)
    {
        abort_if(!in_array($client->id, Auth::user()->stravaClients()->pluck('id')->toArray()), 403, 'You do not have access to this client.');
        abort_if($client->enabled === false, 403, 'You cannot test a disabled client');

        $strava = Strava::client(Auth::user());
        $result = $strava->authentication()->withClient($client)->currentUser();

        return [
            'name' => $result['firstname'] ?? null . ' ' . $result['lastname'],
        ];
    }
}
