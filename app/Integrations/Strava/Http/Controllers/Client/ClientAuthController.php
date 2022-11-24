<?php

namespace App\Integrations\Strava\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Integrations\Strava\Client\Authentication\Authenticator;
use App\Integrations\Strava\Client\Authentication\StravaToken;
use App\Integrations\Strava\Client\Models\StravaClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Spatie\Url\Url;

class ClientAuthController extends Controller
{
    public function initiateLogin(Request $request, StravaClient $client)
    {
        $state = session()->get('stravaState') ?? Str::random(15);
        session()->put('stravaState', $state);

        $stravaUrl = sprintf(
            '%s?client_id=%s&redirect_uri=%s&response_type=code&approval_prompt=auto&scope=%s&state=%s',
            Url::fromString($request->input('mobile', false)
                ? 'https://www.strava.com/oauth/mobile/authorize'
                : 'https://www.strava.com/oauth/authorize'),
            $client->client_id,
            urlencode(route('strava.client.login', $client->id)),
            'activity:read,read,read_all,profile:read_all,activity:read_all,activity:write',
            $state
        );

        return redirect()->to($stravaUrl);
    }

    public function login(Request $request, StravaClient $client, Authenticator $authenticator)
    {
        $request->validate([
            'code' => 'required|string',
            'state' => 'required|string',
        ]);

        abort_if($request->input('state') !== $request->session()->get('stravaState'), 403, 'The states do not match.');
        abort_if(!in_array($client->id, Auth::user()->stravaClients()->pluck('id')->toArray()), 403, 'You do not have access to this client.');
        abort_if($client->isConnected(Auth::id()), 403, 'You are already logged into this client.');

        session()->remove('stravaState');

        $token = $authenticator->exchangeCode($request->input('code'), $client);

        abort_if(
            Auth::user()->hasAdditionalData('strava_athlete_id') && $token->getAthleteId() !== (int) Auth::user()->getAdditionalData('strava_athlete_id'),
            403,
            'You must use the same account to log in to all clients'
        );

        $savedToken = StravaToken::makeFromStravaTokenResponse($token, $client->id);
        $savedToken->save();

        Auth::user()->setAdditionalData('strava_athlete_id', $token->getAthleteId());

        return redirect()->route('integration.strava');
    }

    public function logout(StravaClient $client)
    {
        abort_if(!in_array($client->id, Auth::user()->stravaClients()->pluck('id')->toArray()), 403, 'You do not have access to this client.');
        abort_if(!$client->isConnected(Auth::id()), 403, 'You are not logged into this client.');

        Auth::user()->stravaTokens()->where('strava_client_id', $client->id)->delete();

        return redirect()->route('integration.strava');
    }
}
