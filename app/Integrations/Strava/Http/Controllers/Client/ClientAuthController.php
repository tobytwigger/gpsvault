<?php

namespace App\Integrations\Strava\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Integrations\Strava\Client\Authentication\Authenticator;
use App\Integrations\Strava\Client\Authentication\StravaToken;
use App\Integrations\Strava\Client\Models\StravaClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClientAuthController extends Controller
{

    public function login(Request $request, StravaClient $client, Authenticator $authenticator)
    {
        $request->validate([
            'code' => 'required|string',
//            'state' => 'required|string'
        ]);

//        abort_if($request->input('state') !== $request->session()->get('state'), 403, 'The states do not match.');
        abort_if(!in_array($client->id, Auth::user()->stravaClients()->pluck('id')->toArray()), 403, 'You do not have access to this client.');
        abort_if($client->isConnected(Auth::id()), 403, 'You are already logged into this client.');

        $token = $authenticator->exchangeCode($request->input('code'), $client);

        $savedToken = StravaToken::makeFromStravaTokenResponse($token, $client->id);
        $savedToken->save();

        Auth::user()->setAdditionalData('strava_athlete_id', $token->getAthleteId());

        return redirect()->route('strava.client.index');
    }

    public function logout(StravaClient $client)
    {
        abort_if(!in_array($client->id, Auth::user()->stravaClients()->pluck('id')->toArray()), 403, 'You do not have access to this client.');
        abort_if(!$client->isConnected(Auth::id()), 403, 'You are not logged into this client.');

        Auth::user()->stravaTokens()->where('strava_client_id', $client->id)->delete();

        return redirect()->route('strava.client.index');
    }

}
