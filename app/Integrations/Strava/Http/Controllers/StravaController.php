<?php

namespace App\Integrations\Strava\Http\Controllers;

use App\Integrations\Strava\Models\StravaClient;
use App\Integrations\Strava\StravaToken;
use App\Integrations\Strava\Client\Strava;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class StravaController extends \Illuminate\Routing\Controller
{

    public function login(Request $request, StravaClient $client, Strava $strava)
    {
        $request->session()->put('state', $state = Str::random(40));

        return new RedirectResponse(
            $strava->redirectUrl(
                $state,
                $client
            )
        );
    }

    public function callback(Request $request, StravaClient $client, Strava $strava)
    {
        $request->validate([
            'code' => 'required|string',
            'state' => 'required|string'
        ]);
        abort_if($request->input('state') !== $request->session()->get('state'), 403, 'The states do not match');

        $token = $strava->client()->exchangeCode($request->input('code'), $client);

        $savedToken = StravaToken::makeFromStravaToken($token, $client->id);

        $savedToken->save();

        Auth::user()->setAdditionalData('strava_athlete_id', $token->getAthleteId());

        return redirect()->route('sync.index');
    }

}
